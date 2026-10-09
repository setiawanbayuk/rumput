/* OCR parsing for Indonesian identity cards. No ID or other personal information is stored here. */
(function (root, factory) {
    const api = factory();
    if (typeof module === 'object' && module.exports) module.exports = api;
    if (root) root.EsuketKtpParser = api;
})(typeof window !== 'undefined' ? window : globalThis, function () {
    'use strict';
    const FIELDS = [
        'nik', 'name', 'tempat_lhr', 'tgl_lhr', 'gender', 'gol_darah', 'agama', 'status_kwn',
        'pekerjaan', 'kewarganegaraan', 'alamat', 'provinsi', 'kabko',
        'kecamatan', 'kelurahan', 'rt', 'rw', 'berlaku_hingga'
    ];
    const LABELS = {
        nik: /^N\s*[I1]\s*K\b/i,
        name: /^NAMA\b/i,
        ttl: /^TEMPAT\s*[/,\-]?\s*(?:TGL|TANGGAL)\s*\.?\s*LAHIR\b|^TEMPAT\s+LAHIR\b/i,
        gender: /^JENIS\s+KELAMIN\b/i,
        blood: /^GOL\.?\s*(?:DARAH|DRH)\b/i,
        agama: /^AGAMA\b/i,
        status_kwn: /^STATUS\s*PERKAWINAN\b|^PERKAWINAN\b/i,
        pekerjaan: /^PEKERJAAN\b/i,
        kewarganegaraan: /^KEWARGANEGARAAN\b/i,
        alamat: /^ALAMAT\b/i,
        rt_rw: /^RT\s*[/\\|]\s*RW\b|^RT\s+RW\b/i,
        kelurahan: /^(?:KEL\s*[/\\.]\s*DESA|KELURAHAN|DESA)\b/i,
        kecamatan: /^KECAMATAN\b/i,
        provinsi: /^PROVINSI\b/i,
        kabko: /^(?:KABUPATEN|KAB\.?|KOTA(?:\s+ADMINISTRASI|\s+ADM\.?)?)\s+(?=[A-Z])/i,
        berlaku_hingga: /^BERLAKU\s+HINGGA\b/i
    };
    const KNOWN_LABELS = Object.values(LABELS);
    function clean(value) {
        return String(value || '').replace(/[\u2013\u2014]/g, '-').replace(/\s+/g, ' ').trim().replace(/^[\s:;,.=_\-]+|[\s:;,.=_\-]+$/g, '');
    }
    function normalize(value) { return clean(value).toUpperCase().replace(/[^A-Z0-9]/g, ''); }
    function isLabel(line) { return KNOWN_LABELS.some(re => re.test(line)); }
    function stripFollowingLabels(value) {
        return clean(String(value).split(/\s{2,}(?=(?:NIK|NAMA|AGAMA|RT\s*[/\\]\s*RW|KEL\s*[/\\.]\s*DESA|KECAMATAN|STATUS\s+PERKAWINAN|PEKERJAAN|KEWARGANEGARAAN|BERLAKU\s+HINGGA)\b)/i)[0]);
    }
    function getValue(lines, label) {
        for (let i = 0; i < lines.length; i++) {
            const matched = lines[i].match(LABELS[label]);
            if (!matched) continue;
            let value = clean(lines[i].slice(matched[0].length));
            if (!value && i + 1 < lines.length && !isLabel(lines[i + 1])) {
                // Avoid swallowing a subsequent unlabeled city header.
                const next = clean(lines[i + 1]);
                if (next && next.length < 70) value = next;
            }
            if (value) return stripFollowingLabels(value);
        }
        return '';
    }
    function dateISO(value) {
        const match = String(value || '').match(/(?:^|\D)([0-3]?\d)\s*[\/,.-]\s*([01]?\d)\s*[\/,.-]\s*((?:19|20)\d{2})(?!\d)/);
        if (!match) return '';
        const day = Number(match[1]), month = Number(match[2]), year = Number(match[3]);
        const dt = new Date(Date.UTC(year, month - 1, day));
        if (dt.getUTCDate() !== day || dt.getUTCMonth() !== month - 1 || dt.getUTCFullYear() !== year || dt > new Date()) return '';
        return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    }
    function nikFrom(text) {
        const original = clean(text).toUpperCase();
        const candidate = original.match(/[0-9OQDILSB|][0-9OQDILSB|\s.\-]{13,22}[0-9OQDILSB|]/);
        if (!candidate) return '';
        const fixed = candidate[0].replace(/[\s.\-]/g, '').replace(/[OQD]/g,'0').replace(/[IL|]/g,'1').replace(/S/g,'5').replace(/B/g,'8');
        return /^\d{16}$/.test(fixed) ? fixed : '';
    }
    function vocabulary(value, options) {
        const v = clean(value).toUpperCase().replace(/^[^A-Z]+/, '');
        // Whole-word categories only. Any OCR noise beyond the word is discarded.
        const sorted = options.slice().sort((a,b) => b.length - a.length);
        for (const opt of sorted) {
            const regex = new RegExp('^' + opt.replace(/[.*+?^${}()|[\]\\]/g, '\\$&').replace(/ /g, '\\s*') + '(?:$|[^A-Z])', 'i');
            if (regex.test(v)) return opt;
        }
        return '';
    }
    function status(value) {
        const text = normalize(value);
        if (/^BELUMKAWIN/.test(text)) return 'BELUM KAWIN';
        if (/^CERAIMATI/.test(text)) return 'CERAI MATI';
        if (/^CERAIHIDUP/.test(text)) return 'CERAI HIDUP';
        if (/^KAWIN/.test(text) || /^MENIKAH/.test(text)) return 'KAWIN';
        return '';
    }
    function citizen(value) {
        const text = normalize(value);
        if (/^WNI/.test(text) || /^INDONESIA/.test(text)) return 'WNI';
        if (/^WNA/.test(text) || /^ASING/.test(text)) return 'WNA';
        return '';
    }
    function gender(value) {
        const text = normalize(value);
        if (/PEREMPUAN/.test(text)) return 'PEREMPUAN';
        if (/^LAK[I1]LAK[I1]/.test(text) || /^LAK[I1]/.test(text)) return 'LAKI-LAKI';
        return '';
    }
    function agama(value) {
        const text = normalize(value);
        const map = {
            ISLAM: 'ISLAM', KRISTENPROTESTAN:'KRISTEN', PROTESTAN:'KRISTEN', KRISTEN:'KRISTEN',
            KHATOLIK:'KATOLIK', KATHOLIK:'KATOLIK', KATOLIK:'KATOLIK',
            HINDU:'HINDU', BUDDHA:'BUDDHA', BUDHA:'BUDDHA', KONGHUCU:'KONGHUCU', KONGHUCHU:'KONGHUCU',
            KEPERCAYAAN:'KEPERCAYAAN'
        };
        return Object.keys(map).sort((a,b) => b.length - a.length).map(key => text.startsWith(key) ? map[key] : '').find(Boolean) || '';
    }
    function safeName(value, max = 60) {
        const text = clean(value).toUpperCase();
        if (!text || text.length > max || /\d{3,}/.test(text)) return '';
        const reduced = text.replace(/\s+(?:JENIS KELAMIN|TEMPAT.*LAHIR|GOL DARAH|AGAMA|ALAMAT|RT\/RW|KELURAHAN|KECAMATAN).*$/i, '');
        return /^[A-Z][A-Z\s.'\-]{1,}$/.test(reduced) ? reduced : '';
    }
    function region(value) {
        let text = clean(value).toUpperCase();
        text = text.replace(/^(?:PROVINSI|KECAMATAN|KEL\s*[/\\.]\s*DESA|KELURAHAN|DESA)\s*/i, '');
        text = text.replace(/\s+(?:RT\s*[/\\]\s*RW|PEKERJAAN|KEWARGANEGARAAN|BERLAKU\s+HINGGA).*$/i, '');
        if (!text || text.length > 48 || /\d{3,}/.test(text)) return '';
        return text.replace(/[^A-Z0-9 .\-\/]/g, '').trim();
    }
    function parse(rawText) {
        const lines = String(rawText || '').split(/\r?\n/).map(clean).filter(Boolean);
        const result = {};
        const set = (key, value) => { if (value) result[key] = value; };
        const nikValue = getValue(lines, 'nik');
        set('nik', nikFrom(nikValue));
        if (!result.nik) {
            const anywhere = String(rawText || '').match(/(?:^|\D)(\d{16})(?!\d)/);
            if (anywhere) set('nik', anywhere[1]);
        }
        set('name', safeName(getValue(lines,'name')));
        const ttl = getValue(lines, 'ttl');
        const matchedDate = ttl.match(/([0-3]?\d\s*[\/,.-]\s*[01]?\d\s*[\/,.-]\s*(?:19|20)\d{2})/);
        if (matchedDate) {
            set('tgl_lhr', dateISO(matchedDate[1]));
            set('tempat_lhr', region(ttl.slice(0, matchedDate.index).replace(/[,\-\s]+$/, '')));
        } else if (ttl) {
            set('tempat_lhr', safeName(ttl, 40));
        }
        set('gender', gender(getValue(lines,'gender')));
        const embeddedBlood = lines.map(line => (line.match(/(?:GOL\.?\s*(?:DARAH|DRH))\s*[:.\-\s]+(AB|A|B|O)[+\-]?/i) || [])[0] || '').find(Boolean);
        const blood = normalize(getValue(lines, 'blood') || (embeddedBlood || '').replace(/^(?:GOL\.?\s*(?:DARAH|DRH))\s*[:.\-\s]+/i, ''));
        const bloodMatch = blood.match(/^(AB|A|B|O)([+-])?/);
        if (bloodMatch) set('gol_darah', bloodMatch[1] + (bloodMatch[2] || ''));
        set('agama', agama(getValue(lines,'agama')));
        set('status_kwn', status(getValue(lines,'status_kwn')));
        const job = clean(getValue(lines,'pekerjaan')).toUpperCase().replace(/\s+(?:KEWARGANEGARAAN|BERLAKU HINGGA).*$/i, '');
        set('pekerjaan', safeName(job, 60));
        set('kewarganegaraan', citizen(getValue(lines,'kewarganegaraan')));
        const address = clean(getValue(lines, 'alamat')).toUpperCase();
        if (address && address.length <= 110) set('alamat', address.replace(/\s+(?:RT\s*[/\\]\s*RW|KEL\/DESA|KECAMATAN).*$/i, ''));
        const rtRw = getValue(lines,'rt_rw') || (String(rawText).match(/\bRT\s*[\/:]\s*RW\s*[:\s]*([0-9O]{1,3}\s*[/\\-]\s*[0-9O]{1,3})/i) || [,''])[1];
        const parts = rtRw.replace(/O/gi,'0').match(/(\d{1,3})\s*[/\\-]\s*(\d{1,3})/);
        if (parts) { set('rt',parts[1]); set('rw',parts[2]); }
        set('kelurahan', region(getValue(lines, 'kelurahan')));
        set('kecamatan', region(getValue(lines,'kecamatan')));
        set('provinsi', region(getValue(lines,'provinsi')));
        // City/kabupaten header appears between the province and NIK, never in citizenship text.
        for (const line of lines.slice(0, Math.max(6, lines.findIndex(x => LABELS.nik.test(x)) + 1))) {
            const match = line.match(LABELS.kabko);
            if (!match) continue;
            const name = region(line.slice(match[0].length));
            if (name) { set('kabko', line.match(/^\s*KAB/i) ? 'KABUPATEN ' + name : 'KOTA ' + name); break; }
        }
        const validity = getValue(lines,'berlaku_hingga');
        if (/SEUMUR\s+HIDUP/i.test(validity)) set('berlaku_hingga', 'SEUMUR HIDUP');
        else set('berlaku_hingga', dateISO(validity));
        return result;
    }
    function parseCandidates(texts) {
        const candidates = (Array.isArray(texts) ? texts : [texts]).map(parse);
        const result = {}, warnings = [];
        for (const field of FIELDS) {
            const values = candidates.map(x => x[field]).filter(Boolean);
            if (!values.length) continue;
            const counts = new Map();
            for (const v of values) counts.set(v, (counts.get(v) || 0) + 1);
            const winning = [...counts].sort((a,b) => b[1] - a[1])[0];
            result[field] = winning[0];
            if (counts.size > 1) warnings.push('Hasil OCR ' + field + ' berbeda pada pembacaan ulang. Periksa dengan KTP.');
        }
        return { data: result, warnings };
    }
    return { parse, parseCandidates, dateISO, normalize };
});
