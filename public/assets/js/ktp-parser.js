/* ESUKET OCR KTP v15: strict OCR-only identity, no fabricated content or residents lookup.
 * Pure browser/parser module, no network, persistent storage, or processing of originals on servers.
 */
(function (root, factory) {
  'use strict';
  const api=factory();
  if(typeof module==='object'&&module.exports)module.exports=api;
  if(root)root.EsuketKtpParser=api;
})(typeof window!=='undefined'?window:globalThis, function () {
  'use strict';
  const FIELDS=['nik','name','tempat_lhr','tgl_lhr','gender','gol_darah','agama','status_kwn',
    'pekerjaan','kewarganegaraan','alamat','provinsi','kabko','kecamatan','kelurahan','rt','rw','berlaku_hingga'];
  const PATTERNS={
    nik:/^N\s*[I1L]\s*K\b/i,
    name:/^N\s*A\s*M\s*A\b/i,
    ttl:/^(?:TEMPAT\s*[/,.\-]?\s*(?:TGL|TANGGAL)\s*\.?\s*LAHIR|TEMPAT\s+LAHIR|TEMPAT\s*[\/:]\s*TGL\s*LAHIR)\b/i,
    gender:/^JENIS\s+KELAMIN\b/i,
    blood:/^GOL\.?\s*(?:DARAH|DRH)\b/i,
    agama:/^AGAMA\b/i,
    status_kwn:/^(?:STATUS\s*PERKAWINAN|PERKAWINAN)\b/i,
    pekerjaan:/^PEKERJAAN\b/i,
    kewarganegaraan:/^KEWARGANEGARAAN\b/i,
    alamat:/^ALAMAT\b/i,
    // RT/RW sering dibaca RT/AW atau RT/RV oleh OCR pada cetakan KTP.
    rt_rw:/^R\s*T\s*[\/\\|:]\s*[RA]\s*[WV]\b|^RT\s+RW\b/i,
    kelurahan:/^(?:KEL\s*[\/\.\-]\s*DESA|KELURAHAN|DESA)\b/i,
    kecamatan:/^KECAMATAN\b/i,
    provinsi:/^PROVINSI\b/i,
    kabko:/^(?:KABUPATEN|KAB\.?|KOTA(?:\s+ADMINISTRASI|\s+ADM\.?)?)\s+(?=[A-Z])/i,
    berlaku_hingga:/^BERLAKU\s+HINGGA\b/i
  };
  const ANY_LABEL='(?:NIK|NAMA|TEMPAT\\s*[/,:.\\-]?\\s*T(?:GL|ANGGAL)\\s*LAHIR|JENIS\\s+KELAMIN|GOL\\.?\\s*DARAH|AGAMA|STATUS\\s+PERKAWINAN|PEKERJAAN|KEWARGANEGARAAN|ALAMAT|RT\\s*[\\/\\\\|:]\\s*RW|KEL\\s*[\\/\\.\\-]\\s*DESA|KELURAHAN|KECAMATAN|BERLAKU\\s+HINGGA)';
  const INLINE_LABEL = new RegExp('\\s{2,}(?='+ANY_LABEL+'\\b)', 'gi');
  function clean(text){return String(text??'').replace(/[\u2013\u2014]/g,'-').replace(/\u00a0/g,' ').replace(/\s+/g,' ').trim().replace(/^[\s:;,.=_\-]+|[\s:;,.=_\-]+$/g,'');}
  function normalize(text){return clean(text).toUpperCase().replace(/[^A-Z0-9]/g,'');}
  function linesOf(raw){
    return String(raw??'').replace(/\u00a0/g,' ').split(/\r?\n/).flatMap(line=>line.replace(INLINE_LABEL,'\n').split('\n'))
      .map(clean).filter(Boolean);
  }
  function isLabel(line){return Object.values(PATTERNS).some(re=>re.test(line));}
  function stripNextLabels(text){
    const raw=clean(text);
    const match=raw.match(new RegExp('\\s{2,}(?='+ANY_LABEL+'\\b)','i'));
    return match?clean(raw.slice(0,match.index)):raw;
  }
  function valueOf(lines,label) {
    const regexp=PATTERNS[label];
    for(let i=0;i<lines.length;i++){
      const match=lines[i].match(regexp);if(!match)continue;
      let value=stripNextLabels(lines[i].slice(match[0].length));
      if(!value && lines[i+1]&&!isLabel(lines[i+1]) && lines[i+1].length<80)
        value=stripNextLabels(lines[i+1]);
      if(value)return value;
    }
    return '';
  }
  function dateISO(value){
    const match=String(value||'').match(/(?:^|\D)([0-3]?\d)\s*[\/,.\-]\s*([01]?\d)\s*[\/,.\-]\s*((?:19|20)\d{2})(?!\d)/);
    if(!match)return '';
    const day=+match[1],month=+match[2],year=+match[3];
    const dt=new Date(Date.UTC(year,month-1,day));
    return dt.getUTCDate()===day&&dt.getUTCMonth()===month-1&&dt.getUTCFullYear()===year&&dt<=new Date()
      ?`${year}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`:'';
  }
  function nikFrom(text){
    // No O=>0 / I=>1 substitutions: a fabricated NIK could match someone else.
    const raw=String(text||'');
    const tokens=raw.match(/(?:^|[^0-9])(?:\d[\s.\-]?){16}(?!\d)/g)||[];
    for(const item of tokens){
      const digits=item.replace(/[^0-9]/g,'');
      if(/^\d{16}$/.test(digits)&&!/^0{16}$/.test(digits))return digits;
    }
    return '';
  }
  function strictName(value){
    // Nama tidak diisi jika OCR menyisipkan angka, label lain, atau teks kartu.
    const raw=clean(value).toUpperCase();
    if(raw.length<3||raw.length>60||/\d/.test(raw))return '';
    if(/\b(?:NIK|NAMA|AGAMA|GOL\.?\s*DARAH|PEKERJAAN|KECAMATAN|KELURAHAN|KEWARGANEGARAAN|BERLAKU|PROVINSI|JENIS\s+KELAMIN|TEMPAT|TGL|TANGGAL|ALAMAT|STATUS\s+PERKAWINAN|RT\s*[/\-]\s*RW)\b/.test(raw))return '';
    if(/^(?:KTP|REPUBLIK\s+INDONESIA|WARGA\s+NEGARA\s+INDONESIA|LAKI[\s-]*LAKI|PEREMPUAN)$/.test(raw))return '';
    if(!/^[A-Z][A-Z .\'\-]{1,58}[A-Z.]$/.test(raw))return '';
    return raw.replace(/\s+/g,' ').trim();
  }
  function firstKnown(value,items){const v=normalize(value);return items.find(([key])=>v.startsWith(key))?.[1]||'';}
  function gender(value){const v=normalize(value);if(v.includes('PEREMPUAN'))return 'PEREMPUAN';if(/LAK[I1]LAK[I1]/.test(v)||/^LAK[I1]/.test(v))return 'LAKI-LAKI';return '';}
  function religion(value){return firstKnown(value,[['KRISTENPROTESTAN','KRISTEN'],['PROTESTAN','KRISTEN'],['KRISTEN','KRISTEN'],['KATHOLIK','KATOLIK'],['KHATOLIK','KATOLIK'],['KATOLIK','KATOLIK'],['BUDDHA','BUDDHA'],['BUDHA','BUDDHA'],['KONGHUCU','KONGHUCU'],['KONGHUCHU','KONGHUCU'],['ISLAM','ISLAM'],['HINDU','HINDU'],['KEPERCAYAAN','KEPERCAYAAN']]);}
  function marital(value){return firstKnown(value,[['BELUMKAWIN','BELUM KAWIN'],['CERAIMATI','CERAI MATI'],['CERAIHIDUP','CERAI HIDUP'],['MENIKAH','KAWIN'],['KAWIN','KAWIN']]);}
  function nationality(value){return firstKnown(value,[['WNI','WNI'],['WNA','WNA'],['INDONESIA','WNI'],['ASING','WNA']]);}
  function alphaText(value,max=65){
    const s=clean(value).toUpperCase().replace(new RegExp('\\s{2,}(?='+ANY_LABEL+'\\b)','i'),'').trim();
    if(!s||s.length>max||/\d{3,}/.test(s))return '';
    const safe=s.replace(/[,:;_+]+$/,'').trim();
    return /[A-Z]{2}/.test(safe)&&/^[A-Z0-9][A-Z0-9\s.'\/()&\-]*$/.test(safe)?safe:'';
  }
  function address(value){
    let s=clean(value).toUpperCase();
    s=s.replace(/\s+(?:RT\s*[\/\\|:]\s*RW|KEL\s*[\/\.]\s*DESA|KELURAHAN|KECAMATAN)\b.*$/i,'');
    return s.length<=130 && /[A-Z]{2}/.test(s)?s:'';
  }
  function region(value){
    let s=clean(value).toUpperCase();
    s=s.replace(/^(?:PROVINSI|KECAMATAN|KEL\s*[\/\.]\s*DESA|KELURAHAN|DESA)\s*[:.\-]?\s*/i,'');
    s=s.replace(/\s+(?:RT\s*[\/\\|:]\s*RW|PEKERJAAN|KEWARGANEGARAAN|BERLAKU\s+HINGGA)\b.*$/i,'');
    if(!s||s.length>58||/\d{3,}/.test(s))return '';
    return s.replace(/[^A-Z0-9 .\-\/]/g,'').trim();
  }
  function parse(raw){
    const lines=linesOf(raw);const result={};const put=(k,v)=>{if(v)result[k]=v;};
    put('nik',nikFrom(valueOf(lines,'nik'))||nikFrom(String(raw).match(/(?:N\s*[I1]\s*K.{0,30})/i)?.[0]||''));
    put('name',strictName(valueOf(lines,'name')));
    const ttl=valueOf(lines,'ttl');
    const d=ttl.match(/([0-3]?\d\s*[\/,.\-]\s*[01]?\d\s*[\/,.\-]\s*(?:19|20)\d{2})/);
    if(d){put('tgl_lhr',dateISO(d[1]));put('tempat_lhr',alphaText(ttl.slice(0,d.index).replace(/[,\-\s]+$/,''),45));}
    else if(ttl)put('tempat_lhr',alphaText(ttl,45));
    put('gender',gender(valueOf(lines,'gender')));
    const embeddedBlood=(String(raw||'').match(/\bGOL\.?\s*(?:DARAH|DRH)\s*[:.\-\s]+(AB|A|B|O)(?:[+\-])?/i)||[])[1]||'';
    const blood=normalize(valueOf(lines,'blood')||embeddedBlood);
    // Golongan darah yang tidak tercantum/terbaca tidak boleh ditebak.
    const bloodResult=blood.match(/^(AB|A|B|O)([+-])?$/);
    if(bloodResult)put('gol_darah',bloodResult[1]+(bloodResult[2]||''));
    put('agama',religion(valueOf(lines,'agama')));
    put('status_kwn',marital(valueOf(lines,'status_kwn')));
    put('pekerjaan',alphaText(valueOf(lines,'pekerjaan').replace(/\s+(?:KEWARGANEGARAAN|BERLAKU\s+HINGGA)\b.*$/i,''),65));
    put('kewarganegaraan',nationality(valueOf(lines,'kewarganegaraan')));
    put('alamat',address(valueOf(lines,'alamat')));
    const rtRw=valueOf(lines,'rt_rw')||(String(raw).match(/\bR\s*T\s*[\/\\|:]\s*[RA]\s*[WV]\s*[:\s-]*([0-9]{1,3}\s*[\/\\|\-]\s*[0-9]{1,3})/i)||[,''])[1];
    const pair=rtRw.match(/(\d{1,3})\s*[\/\\|\-]\s*(\d{1,3})/);
    if(pair){put('rt',pair[1]);put('rw',pair[2]);}
    put('kelurahan',region(valueOf(lines,'kelurahan')));
    put('kecamatan',region(valueOf(lines,'kecamatan')));
    put('provinsi',region(valueOf(lines,'provinsi')));
    // Indonesian province headers may lack the 'PROVINSI' prefix, especially DKI Jakarta.
    const posNik=lines.findIndex(x=>PATTERNS.nik.test(x));
    const header=lines.slice(0,posNik>=0?Math.min(8,posNik):Math.min(5,lines.length));
    if(!result.provinsi){
      const province=header.find(x=>/^(?:DKI\s+JAKARTA|DI\s+YOGYAKARTA|DAERAH\s+ISTIMEWA\s+YOGYAKARTA|JAWA\s+(?:TIMUR|TENGAH|BARAT)|BANTEN|BALI)\b/i.test(x));
      if(province)put('provinsi',region(province));
    }
    for(const line of header){
      const match=line.match(PATTERNS.kabko);if(!match)continue;
      const locality=region(line.slice(match[0].length));
      if(locality) {put('kabko',(line.match(/^KAB/i)?'KABUPATEN ':'KOTA ')+locality);break;}
    }
    const expiry=valueOf(lines,'berlaku_hingga');
    if(/SEUMUR\s+HIDUP/i.test(expiry))put('berlaku_hingga','SEUMUR HIDUP');
    else put('berlaku_hingga',dateISO(expiry));
    return result;
  }
  function fieldScore(field,value){
    // Used only to distinguish valid candidate strings, never to invent data.
    if(!value)return 0;
    if(field==='nik')return /^\d{16}$/.test(value)?200:0;
    if(field==='name')return /^[A-Z][A-Z\s.\-'()]{2,60}$/.test(value)?150:0;
    if(['gender','agama','status_kwn','kewarganegaraan'].includes(field))return 130;
    if(field==='tgl_lhr')return /^\d{4}-\d\d-\d\d$/.test(value)?120:0;
    if(field==='rt'||field==='rw')return /^\d{1,3}$/.test(value)?90:0;
    return 70+Math.min(String(value).length,36)/10;
  }
  function parseCandidates(texts){
    const passes=(Array.isArray(texts)?texts:[texts]).map(parse);
    const data={},warnings=[],scores={},conflicts=[];
    for(const field of FIELDS){
      const found=passes.map((entry,i)=>({value:entry[field],i})).filter(x=>!!x.value);
      if(!found.length)continue;
      const groups=new Map();
      for(const item of found){
        const key=(field==='rt'||field==='rw')?String(Number(item.value)):normalize(item.value);
        if(!groups.has(key))groups.set(key,[]);
        groups.get(key).push(item);
      }
      const distinct=[...groups.values()].sort((a,b)=>b.length-a.length||fieldScore(field,b[0].value)-fieldScore(field,a[0].value));
      const best=distinct[0],other=distinct.slice(1);
      // Identity is high-stakes: ALL observed NIK/name readings must agree.
      // A majority vote never silently overrides a conflicting identity string.
      if(['nik','name'].includes(field)){
        if(other.length){
          conflicts.push(field);
          warnings.push(field+' berbeda antarpembacaan: jangan isi otomatis. Periksa KTP asli.');
          continue;
        }
        if(best.length<2){
          warnings.push(field+' hanya terlihat pada satu hasil OCR; wajib dikonfirmasi manual.');
          // Keep a strict 16-digit NIK as a lookup candidate only. The caller
          // must still check the residents record before using it.
          if(field==='name')continue;
        }
      }else if(other.length){
        // Ambiguous non-identity values stay blank too; do not choose by score.
        conflicts.push(field);
        warnings.push(field+' tidak konsisten; wajib diperiksa.');
        continue;
      }
      const selected=best.slice().sort((a,b)=>fieldScore(field,b.value)-fieldScore(field,a.value))[0].value;
      if(!fieldScore(field,selected))continue;
      data[field]=selected;
      scores[field]=best.length/Math.max(1,found.length);
      if(best.length===1&&passes.length>1)warnings.push(field+' hanya dikenali pada satu pembacaan; periksa lagi.');
    }
    return {data,warnings,scores,conflicts,reads:passes};
  }
  return {parse,parseCandidates,dateISO,normalize};
});
