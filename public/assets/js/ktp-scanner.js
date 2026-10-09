/* ESUKET OCR KTP v8.2 (kamera HP selaras halaman tes; diagnostik konteks aktual): crop to real KTP borders, validate OCR labels, match Select2 master data.
   Client-side OCR only. No image upload or persistence. */
(function () {
    'use strict';
    const CDN = 'https://cdn.jsdelivr.net/npm/tesseract.js@5.1.1/dist/tesseract.min.js';
    const SIZE_LIMIT = 12 * 1024 * 1024;
    const RATIO = 85.6 / 54;
    const FIELDS = {
        nik:'NIK', name:'Nama', tempat_lhr:'Tempat lahir', tgl_lhr:'Tanggal lahir',
        gender:'Jenis kelamin', gol_darah:'Golongan darah', agama:'Agama', status_kwn:'Status perkawinan',
        pekerjaan:'Pekerjaan', kewarganegaraan:'Kewarganegaraan', alamat:'Alamat',
        provinsi:'Provinsi',kabko:'Kabupaten/Kota',kecamatan:'Kecamatan',kelurahan:'Kelurahan',
        rt:'RT',rw:'RW',berlaku_hingga:'Berlaku hingga'
    };
    const IDS = Object.keys(FIELDS);
    const BASIC = ['nik','name','tempat_lhr','tgl_lhr','alamat'];
    const DIRECT = ['gender','agama','status_kwn','pekerjaan','kewarganegaraan'];
    const REGIONS = [
        ['provinsi',null],['kabko','kode_provinsi'],['kecamatan','kode_kabkota'],
        ['kelurahan','kode_kecamatan'],['rw','kode_kelurahan'],['rt','rw']
    ];
    const PARENTS = { kode_provinsi:'provinsi',kode_kabkota:'kabko',kode_kecamatan:'kecamatan',kode_kelurahan:'kelurahan',rw:'rw' };
    const clamp = (v,min,max) => Math.min(max,Math.max(min,v));
    document.addEventListener('DOMContentLoaded', () => {
        const root=document.getElementById('esuket-ktp-scanner');
        if (!root) return;
        const el=id=>document.getElementById('ktp-'+id);
        const canvas=el('stage');
        const context=canvas.getContext('2d');
        let image=null, release=()=>{}, selectedFile=null;
        let crop=null, display=null, drag=null, videoStream=null, reading=false;
        let latest=null, latestWarnings=[], library=null, modelLoading=null;
        let cameraTimer=null, capturePending=false, cameraStartedAt=0, stillFrames=0, previousLuma=null;
        let lastCameraAssessment=null;
        let jobId=0; // cegah hasil pemindaian lama mengisi form setelah pengguna mengganti foto
        let lastApplyReport=null;
        const qualityCanvas=document.createElement('canvas');
        qualityCanvas.width=320;qualityCanvas.height=Math.round(320/RATIO);
        const qualityContext=qualityCanvas.getContext('2d',{willReadFrequently:true});
        function status(message,kind='muted') {
            el('status').textContent=message;
            el('status').className='ktp-status '+(kind==='error'?'text-danger':kind==='ok'?'text-success':'text-muted');
            el('compact-status').textContent=message;
            el('compact-status').className='ktp-compact-text '+(kind==='error'?'text-danger':kind==='ok'?'text-success':'text-muted');
        }
        function collapseScanner() {
            root.classList.add('ktp-compact');
            el('dialog-overlay').classList.add('d-none');
            el('compact-bar').classList.remove('d-none');
            el('compact-review').classList.toggle('d-none',!latest);
        }
        function expandScanner() {
            root.classList.remove('ktp-compact');
            el('dialog-overlay').classList.remove('d-none');
            el('compact-bar').classList.add('d-none');
        }
        function focusForm() {
            const anchor=document.getElementById('nik');
            if (!anchor) return;
            // Header ESUKET fixed; posisikan data warga tepat di bawah header tanpa mengubah fokus keyboard.
            const top=anchor.getBoundingClientRect().top + window.scrollY - 180;
            if (typeof window.scrollTo==='function') window.scrollTo({top:Math.max(0,top),behavior:'smooth'});
        }
        function busy(yes) {
            reading=yes;
            ['read','rescan','open-file','open-camera','capture','refocus','reset-crop','new-crop','clear','apply'].forEach(id=>{
                const btn=el(id); if(btn) btn.disabled=yes;
            });
        }
        function progress(percent) {
            const val=clamp(Math.round(percent),0,100);
            el('progress-wrap').classList.remove('d-none');
            el('progress').style.width=val+'%';
            el('progress-wrap').setAttribute('aria-valuenow',String(val));
            el('compact-progress-wrap').classList.remove('d-none');
            el('compact-progress').style.width=val+'%';
            el('compact-progress-wrap').setAttribute('aria-valuenow',String(val));
        }
        function stopCamera() {
            if(cameraTimer!==null){clearInterval(cameraTimer);cameraTimer=null;}
            stillFrames=0;previousLuma=null;lastCameraAssessment=null;
            root.classList.remove('ktp-guide-ready');
            el('capture').disabled=true;
            if (videoStream) videoStream.getTracks().forEach(x=>x.stop());
            videoStream=null;
            el('video').srcObject=null;
            el('camera-area').classList.add('d-none');
        }
        // Autofocus settings vary by camera hardware and browser; unsupported modes are optional.
        async function requestFocus(singleShot=false) {
            const track=videoStream?.getVideoTracks()?.[0];
            if(!track?.getCapabilities||!track.applyConstraints) return false;
            let capabilities;
            try {capabilities=track.getCapabilities();} catch(_) {return false;}
            const modes=capabilities.focusMode||[];
            const wished=singleShot?['single-shot','continuous','auto']:['continuous','auto','single-shot'];
            const mode=wished.find(x=>modes.includes(x));
            if(!mode) return false;
            try {await track.applyConstraints({advanced:[{focusMode:mode}]});return true;}
            catch(_){return false;}
        }
        // Sumber frame yang tepat sama dengan video {object-fit: cover} pada area panduan.
        // Nilai 8% -> 92% SELALU mengacu pada video yang TERLIHAT, bukan resolusi sensor.
        function visibleCameraRect(video) {
            const w=video.videoWidth,h=video.videoHeight;
            if(w/h>RATIO){const vw=h*RATIO;return {x:(w-vw)/2,y:0,w:vw,h};}
            const vh=w/RATIO;
            return {x:0,y:(h-vh)/2,w,h:vh};
        }
        // Estimasi 4 tepi KTP dalam garis putih; pembacaan ini adalah deteksi visual lokal,
        // bukan bukti identitas KTP / pembacaan OCR yang pasti.
        function detectCenteredCard(luma,width,height) {
            const at=(x,y)=>luma[y*width+x];
            const scoreVertical=(x,lo,hi)=>{
                let hits=0,total=0;
                for(let y=Math.round(height*lo);y<=height*hi;y+=2){
                    if(x<3||x>=width-3||y>=height)continue;
                    if(Math.abs(at(x+2,y)-at(x-2,y))>28)hits++;
                    total++;
                }
                return total?hits/total:0;
            };
            const scoreHorizontal=(y,lo,hi)=>{
                let hits=0,total=0;
                for(let x=Math.round(width*lo);x<=width*hi;x+=2){
                    if(y<3||y>=height-3||x>=width)continue;
                    if(Math.abs(at(x,y+2)-at(x,y-2))>28)hits++;
                    total++;
                }
                return total?hits/total:0;
            };
            // Tepi kartu harus berada DI DALAM garis 8%, tetapi mendekati keempat sisinya.
            // Cegah gambar penuh tulisan di meja dianggap KTP hanya karena ramai tekstur.
            function bestEdge(type,start,end) {
                let best=null;
                const count=type==='vertical'?width:height;
                for(let p=Math.round(count*start);p<=Math.round(count*end);p++){
                    const sections=type==='vertical'
                        ?[scoreVertical(p,.23,.43),scoreVertical(p,.57,.77)]
                        :[scoreHorizontal(p,.22,.42),scoreHorizontal(p,.58,.78)];
                    const strength=Math.min(...sections);
                    if(!best||strength>best.score)best={p:p/count,score:strength};
                }
                return best;
            }
            const left=bestEdge('vertical',.085,.23);
            const right=bestEdge('vertical',.77,.915);
            const top=bestEdge('horizontal',.085,.23);
            const bottom=bestEdge('horizontal',.77,.915);
            if(!left||!right||!top||!bottom)return {aligned:false,reason:'Bingkai KTP belum terdeteksi.'};
            const xspan=right.p-left.p,yspan=bottom.p-top.p;
            const centered=Math.abs((right.p+left.p)/2-.5)<.055 && Math.abs((bottom.p+top.p)/2-.5)<.055;
            const sizeOk=xspan>=.67&&yspan>=.67&&xspan<=.86&&yspan<=.86;
            const ratioOk=(xspan/yspan)>.82&&(xspan/yspan)<1.18;
            const borderOk=[left,right,top,bottom].every(x=>x.score>=.29);
            return {
                aligned:Boolean(centered&&sizeOk&&ratioOk&&borderOk),
                borders:[left.score,right.score,top.score,bottom.score],
                center:{x:(left.p+right.p)/2,y:(top.p+bottom.p)/2},
                reason:!borderOk?'Arahkan keempat sisi KTP tepat di dalam garis putih.'
                    :!centered?'Geser KTP ke tengah garis putih.'
                    :!sizeOk?'Atur jarak hingga seluruh KTP pas di bingkai putih.'
                    :!ratioOk?'Luruskan KTP, jangan terlalu miring.':'KTP berada di tengah bingkai.'
            };
        }
        function cameraQuality() {
            const video=el('video');
            if(!videoStream||video.readyState<2||video.videoWidth<320||video.videoHeight<200)return null;
            const rect=visibleCameraRect(video);
            qualityContext.drawImage(video,rect.x,rect.y,rect.w,rect.h,
                0,0,qualityCanvas.width,qualityCanvas.height);
            const {data}=qualityContext.getImageData(0,0,qualityCanvas.width,qualityCanvas.height);
            const width=qualityCanvas.width,height=qualityCanvas.height;
            const luma=new Uint8Array(width*height);
            let brightness=0,variance=0,edges=0,motion=0,edgeCount=0;
            for(let p=0,i=0;p<data.length;p+=4,i++){
                const y=Math.round(data[p]*.299+data[p+1]*.587+data[p+2]*.114);
                luma[i]=y;brightness+=y;
                if(previousLuma)motion+=Math.abs(y-previousLuma[i]);
            }
            const pixels=luma.length;
            brightness/=pixels;
            for(let y=2;y<height-2;y+=2){
                for(let x=2;x<width-2;x+=2){
                    const i=y*width+x;
                    const d=Math.abs(luma[i]-luma[i-2])+Math.abs(luma[i]-luma[i-width*2]);
                    edges+=d;if(d>35)edgeCount++;
                    variance+=(luma[i]-brightness)**2;
                }
            }
            const samples=Math.floor((height-4)/2)*Math.floor((width-4)/2);
            const contrast=Math.sqrt(variance/samples);
            const texture=edges/samples;
            const density=edgeCount/samples;
            const movement=previousLuma?motion/pixels:999;
            previousLuma=luma;
            const guide=detectCenteredCard(luma,width,height);
            const brightEnough=brightness>55&&brightness<215;
            const detailEnough=contrast>22&&texture>13&&density>.065;
            const steady=movement<11;
            return {brightEnough,detailEnough,steady,guide,movement,contrast,texture,
                ready:brightEnough&&detailEnough&&steady&&guide.aligned};
        }
        function updateCameraFeedback(q) {
            lastCameraAssessment={...q,at:Date.now()};
            // Manual maupun otomatis harus lolos pemeriksaan bingkai dan ketajaman.
            el('capture').disabled=!q.ready;
            root.classList.toggle('ktp-guide-ready',q.ready);
            if(!q.guide.aligned){
                stillFrames=0;
                el('camera-quality').textContent=q.guide.reason;
            } else if(!q.brightEnough){
                stillFrames=0;
                el('camera-quality').textContent='KTP sudah di tengah, tetapi terlalu gelap/silau. Atur cahaya.';
            } else if(!q.detailEnough){
                stillFrames=0;
                el('camera-quality').textContent='KTP sudah di tengah, tetapi masih buram. Dekatkan perlahan dan coba fokus.';
            } else if(!q.steady){
                stillFrames=0;
                el('camera-quality').textContent='KTP sudah di tengah. Tahan kamera agar tidak bergerak.';
            } else {
                stillFrames++;
                el('camera-quality').textContent='KTP terdeteksi dalam garis putih. Stabil '+Math.min(stillFrames,4)+'/4…';
            }
        }
        function startCameraMonitor() {
            if(cameraTimer!==null)clearInterval(cameraTimer);
            stillFrames=0;previousLuma=null;lastCameraAssessment=null;cameraStartedAt=Date.now();
            el('capture').disabled=true;
            cameraTimer=setInterval(()=>{
                if(reading||capturePending||!videoStream)return;
                let q;
                try{q=cameraQuality();}catch(_){return;}
                if(!q)return;
                updateCameraFeedback(q);
                if(el('auto-capture').checked&&q.ready&&stillFrames>=4&&Date.now()-cameraStartedAt>=1900){
                    el('camera-quality').textContent='Bingkai sesuai. Mengambil foto otomatis…';
                    cameraCapture();
                }
            },500);
        }
        function forgetImage() {
            release(); release=()=>{}; image=null; crop=null; display=null; drag=null;
            selectedFile=null; latest=null; latestWarnings=[];
            lastApplyReport=null;
            el('compact-review').classList.add('d-none');
            el('image-area').classList.add('d-none');
            el('results').classList.add('d-none');
            el('file').value='';
        }
        async function openBitmap(file) {
            if (typeof createImageBitmap==='function') {
                const bitmap=await createImageBitmap(file);
                return { img:bitmap, close:()=>bitmap.close() };
            }
            const url=URL.createObjectURL(file);
            try {
                const img=await new Promise((resolve,reject)=>{
                    const photo=new Image(); photo.onload=()=>resolve(photo);
                    photo.onerror=()=>reject(new Error('Gambar tidak dapat dibuka'));
                    photo.src=url;
                });
                return { img,close:()=>{} };
            } finally { URL.revokeObjectURL(url); }
        }
        function fitCrop() {
            if(!image) return;
            // Preserve original pixels; do NOT resize the KTP with stretching or cut it silently.
            const {width:w,height:h}=image;
            if(w/h>RATIO) {
                const cw=h*RATIO;
                crop={x:(w-cw)/2,y:0,w:cw,h};
            } else {
                const ch=w/RATIO;
                crop={x:0,y:(h-ch)/2,w,h:ch};
            }
            renderStage();
        }
        function renderStage() {
            if(!image) return;
            const width=canvas.width,height=canvas.height;
            context.fillStyle='#eff1f3'; context.fillRect(0,0,width,height);
            const scale=Math.min(width/image.width,height/image.height);
            const dw=image.width*scale,dh=image.height*scale;
            display={x:(width-dw)/2,y:(height-dh)/2,w:dw,h:dh,scale};
            context.imageSmoothingQuality='high';
            context.drawImage(image,display.x,display.y,dw,dh);
            if(!crop) return;
            const xx=display.x+crop.x*scale,yy=display.y+crop.y*scale;
            const ww=crop.w*scale,hh=crop.h*scale;
            context.fillStyle='rgba(16,24,40,.40)';
            context.fillRect(0,0,width,Math.max(0,yy));
            context.fillRect(0,yy,Math.max(0,xx),hh);
            context.fillRect(xx+ww,yy,Math.max(0,width-xx-ww),hh);
            context.fillRect(0,yy+hh,width,Math.max(0,height-yy-hh));
            context.strokeStyle='#fff';context.lineWidth=3;
            context.setLineDash([15,8]);
            context.strokeRect(xx,yy,ww,hh);
            context.setLineDash([]);
            context.fillStyle='rgba(10,73,46,.9)';context.fillRect(xx,yy,Math.min(ww,160),30);
            context.fillStyle='#fff';context.font='bold 19px sans-serif';
            context.fillText('AREA KTP',xx+8,yy+21);
        }
        function toPhoto(event) {
            if(!display) return null;
            const rect=canvas.getBoundingClientRect();
            const px=(event.clientX-rect.left)*canvas.width/rect.width;
            const py=(event.clientY-rect.top)*canvas.height/rect.height;
            const x=(px-display.x)/display.scale,y=(py-display.y)/display.scale;
            if(x<0||y<0||x>image.width||y>image.height) return null;
            return {x,y};
        }
        // On image: drag inside selection = move; drag outside = draw a new fixed-aspect selection.
        canvas.addEventListener('pointerdown',event=>{
            if(!image||reading) return;
            const p=toPhoto(event); if(!p) return;
            const within=crop&&p.x>=crop.x&&p.x<=crop.x+crop.w&&p.y>=crop.y&&p.y<=crop.y+crop.h;
            drag=within?{type:'move',x:p.x,y:p.y,old:{...crop}}:{type:'new',x:p.x,y:p.y};
            canvas.setPointerCapture(event.pointerId);
        });
        canvas.addEventListener('pointermove',event=>{
            if(!drag||!image) return;
            const p=toPhoto(event);if(!p) return;
            if(drag.type==='move') {
                crop.x=clamp(drag.old.x+p.x-drag.x,0,image.width-crop.w);
                crop.y=clamp(drag.old.y+p.y-drag.y,0,image.height-crop.h);
            } else {
                const dx=p.x-drag.x,dy=p.y-drag.y;
                const directionX=dx>=0?1:-1,directionY=dy>=0?1:-1;
                let w=Math.min(Math.abs(dx),Math.abs(dy)*RATIO);
                w=Math.max(1,Math.min(w,directionX>0?image.width-drag.x:drag.x,(directionY>0?image.height-drag.y:drag.y)*RATIO));
                const h=w/RATIO;
                crop={x:directionX>0?drag.x:drag.x-w,y:directionY>0?drag.y:drag.y-h,w,h};
            }
            renderStage();
        });
        function endDrag() {
            if(!drag) return;
            drag=null;
            if(crop&&crop.w<Math.min(150,image.width*.15)) fitCrop();
            else renderStage();
        }
        canvas.addEventListener('pointerup',endDrag);
        canvas.addEventListener('pointercancel',endDrag);
        async function setFile(file) {
            if(!file) return;
            if(!['image/jpeg','image/png'].includes(file.type)) {
                collapseScanner(); return status('Gunakan file JPG/JPEG atau PNG.','error');
            }
            if(!file.size||file.size>SIZE_LIMIT) {
                collapseScanner(); return status('File maksimal 12 MB.','error');
            }
            const thisJob=++jobId;
            forgetImage();
            // Begitu JPG diterima / foto kamera diambil, panel besar langsung disembunyikan.
            collapseScanner();
            status('Menyiapkan foto KTP untuk pengisian formulir…');
            try {
                const opened=await openBitmap(file);
                if(thisJob!==jobId){opened.close();return;}
                image=opened.img;release=opened.close;
                selectedFile=file;fitCrop();
                el('image-area').classList.remove('d-none');
                el('filename').textContent=file.name+' · '+(file.size/1048576).toFixed(1)+' MB';
                // Selalu OCR otomatis meski rasio JPG tidak sama persis dengan KTP.
                // Bingkai dapat dibenahi jika pembacaan otomatis gagal/kurang terpercaya.
                recognize();
            } catch(err) {
                if(thisJob!==jobId)return;
                forgetImage();
                expandScanner();
                status('Gagal membuka foto: '+err.message,'error');
            }
        }
        // Tombol Scan Kamera WAJIB memakai video HTML5 di dalam ESUKET.
        // Jangan alihkan otomatis ke input capture="environment"; kamera bawaan HP
        // tidak bisa menampilkan garis putih dan tidak bisa menerapkan validasi bingkai.
        function cameraEnvironment() {
            // Data ini berasal dari halaman Tambah Surat yang AKTIF, bukan halaman tes kamera.
            const loc=window.location||{};
            let inFrame=false;
            try { inFrame=window.self!==window.top; } catch (_) { inFrame=true; }
            const policy=document.permissionsPolicy||document.featurePolicy;
            let cameraAllowed='tidak diketahui';
            try { if(typeof policy?.allowsFeature==='function') cameraAllowed=String(policy.allowsFeature('camera')); } catch (_) {}
            return {
                protocol:loc.protocol||'tidak diketahui', host:loc.host||'tidak diketahui',
                secure:String(window.isSecureContext),
                getUserMedia:typeof navigator.mediaDevices?.getUserMedia,
                inFrame:String(inFrame), cameraAllowed
            };
        }
        function cameraEnvironmentText() {
            const e=cameraEnvironment();
            return '\nPemeriksaan pada halaman Tambah Surat: '+
                'URL='+e.protocol+'//'+e.host+
                ' | secureContext='+e.secure+
                ' | getUserMedia='+e.getUserMedia+
                ' | iframe='+e.inFrame+
                ' | policyKamera='+e.cameraAllowed+
                ' | scanner=v8.2';
        }
        function cameraErrorMessage(err) {
            if (window.isSecureContext === false) {
                return 'HALAMAN TAMBAH SURAT tidak berada dalam konteks aman. Halaman tes mungkin HTTPS, tetapi cek lagi alamat setelah login dan masuk ke Tambah Surat. Harus tetap https://alamat-tunnel.trycloudflare.com.';
            }
            if (!navigator.mediaDevices?.getUserMedia) {
                return 'getUserMedia tidak tersedia pada halaman Tambah Surat, walaupun tes kamera mungkin berhasil. Periksa apakah halaman ini terbuka lewat HTTP atau iframe yang memblokir kamera.';
            }
            if (err?.name === 'NotAllowedError' || err?.name === 'PermissionDeniedError') {
                return 'Izin kamera ditolak atau diblokir browser. Buka ESUKET langsung di Chrome/Safari (bukan browser dalam WhatsApp), izinkan kamera untuk alamat HTTPS ini lalu coba lagi.';
            }
            if (err?.name === 'SecurityError') {
                return 'Kamera diblokir oleh kebijakan keamanan browser atau Permissions-Policy. Buka ESUKET langsung pada tab browser HTTPS, bukan lewat iframe.';
            }
            if (err?.name === 'OverconstrainedError') {
                return 'Pengaturan kamera yang diminta tidak didukung. Coba lagi atau periksa izin kamera.';
            }
            if (err?.name === 'AbortError') {
                return 'Kamera gagal memulai sesi video. Tutup aplikasi kamera lain dan coba lagi.';
            }
            if (err?.name === 'NotFoundError' || err?.name === 'DevicesNotFoundError') {
                return 'Kamera HP tidak ditemukan. Periksa izin/perangkat lalu coba kembali.';
            }
            if (err?.name === 'NotReadableError' || err?.name === 'TrackStartError') {
                return 'Kamera sedang digunakan aplikasi lain. Tutup kamera atau aplikasi lain dan coba kembali.';
            }
            return 'Kamera dalam halaman gagal dibuka. Coba Tes Kamera HP pada URL HTTPS yang sama untuk melihat nama kesalahannya.';
        }
        function showCameraNotice(message) {
            const notice=el('camera-notice');
            notice.textContent=message;
            notice.classList.remove('d-none');
            status(message,'error');
        }
        function hideCameraNotice() {
            const notice=el('camera-notice');
            notice.textContent='';
            notice.classList.add('d-none');
        }
        async function openCamera() {
            if (reading || videoStream) return;
            hideCameraNotice();
            // Kamera TIDAK boleh diblokir oleh kegagalan modul OCR. Periksa modul
            // setelah kamera menyala; ini memisahkan error kamera dari error parser.
            const parserReady=Boolean(window.EsuketKtpParser?.parse);
            // Di HP yang mengakses Laragon melalui alamat IP HTTP, mediaDevices biasanya
            // tidak tersedia. Jangan fallback ke kamera native: panduan akan hilang.
            if (window.isSecureContext === false || !navigator.mediaDevices?.getUserMedia) {
                showCameraNotice(cameraErrorMessage() + cameraEnvironmentText());
                return;
            }
            // Tampilkan garis putih sebelum permintaan izin kamera, sehingga frame
            // tetap bagian dari halaman ESUKET dan bukan aplikasi kamera bawaan.
            stopCamera();
            expandScanner();
            el('camera-area').classList.remove('d-none');
            el('image-area').classList.add('d-none');
            el('results').classList.add('d-none');
            el('camera-quality').textContent='Menghubungkan kamera belakang… Posisikan KTP di bingkai putih.';
            el('camera-focus-help').textContent='Jika muncul permintaan izin kamera, pilih Izinkan.';
            try {
                // SAMAKAN dengan halaman ktp-camera-test.html yang telah berhasil di HP.
                // Tidak memaksa resolusi 1920x1080 pada tahap pembukaan kamera.
                try {
                    videoStream=await navigator.mediaDevices.getUserMedia({
                        audio:false,video:{facingMode:{ideal:'environment'}}
                    });
                } catch (initialError) {
                    if (!['OverconstrainedError','NotFoundError'].includes(initialError?.name)) throw initialError;
                    videoStream=await navigator.mediaDevices.getUserMedia({audio:false,video:true});
                }
                const video=el('video');
                video.srcObject=videoStream;
                await video.play();
                const hasFocus=await requestFocus(false);
                el('camera-focus-help').textContent=hasFocus
                    ? 'Autofocus aktif jika didukung kamera. Tahan HP stabil.'
                    : 'Kontrol fokus otomatis tidak tersedia pada browser ini. Posisikan KTP 15–25 cm dari kamera dan pastikan cahaya terang.';
                el('camera-quality').textContent='Arahkan seluruh sisi KTP ke dalam garis putih…';
                startCameraMonitor();
                if (!parserReady) {
                    // Preview tetap bisa dipakai untuk membuktikan kamera berfungsi.
                    // OCR harus diperbaiki terpisah jika parser gagal dimuat.
                    el('camera-focus-help').textContent += ' CATATAN: modul OCR ktp-parser.js belum termuat; setelah kamera aktif, perbaiki pemanggilan script parser.';
                }
                status('Kamera ESUKET aktif. Semua sisi KTP wajib berada dalam garis putih sebelum foto dapat diambil.');
            } catch(err) {
                stopCamera();
                collapseScanner();
                console.warn('[ESUKET Scan Kamera] gagal:', err?.name || 'Error', err?.message || '');
                showCameraNotice(cameraErrorMessage(err) + (err?.name ? ' [' + err.name + ': ' + (err.message||'') + ']' : '') + cameraEnvironmentText());
            }
        }
        function cameraCapture() {
            const video=el('video');
            if(!videoStream||!video.videoWidth||reading||capturePending)return;
            // Tetap periksa ulang SAAT capture agar foto tidak lolos memakai hasil frame lama.
            let q;
            try{q=cameraQuality();}catch(_){return;}
            if(!q||!q.ready){
                stillFrames=0;
                if(q)updateCameraFeedback(q);
                el('camera-quality').textContent=q?.guide?.reason||'Pastikan KTP di tengah garis putih dan kamera sudah fokus.';
                return;
            }
            capturePending=true;
            const rect=visibleCameraRect(video);
            // Crop tepat mengikuti garis putih: inset 8% pada pratinjau yang terlihat.
            const inset=.08;
            const x=rect.x+rect.w*inset,y=rect.y+rect.h*inset;
            const w=rect.w*(1-2*inset),h=rect.h*(1-2*inset);
            const c=document.createElement('canvas');c.width=Math.round(w);c.height=Math.round(h);
            c.getContext('2d').drawImage(video,x,y,w,h,0,0,c.width,c.height);
            c.toBlob(blob=>{
                capturePending=false;
                if(!videoStream)return; // Kamera sudah ditutup saat konversi berlangsung.
                if(!blob)return status('Gagal mengambil gambar kamera.','error');
                stopCamera();setFile(new File([blob],'ktp-kamera.jpg',{type:'image/jpeg'}));
            },'image/jpeg',.96);
        }
        function canvasForOCR(enhance) {
            const maxW=2600, w=Math.round(Math.min(maxW,Math.max(1300,crop.w))),h=Math.round(w/RATIO);
            const out=document.createElement('canvas');out.width=w;out.height=h;
            const ctx=out.getContext('2d',{willReadFrequently:true});
            ctx.fillStyle='#fff';ctx.fillRect(0,0,w,h);
            ctx.imageSmoothingEnabled=true;ctx.imageSmoothingQuality='high';
            ctx.drawImage(image,crop.x,crop.y,crop.w,crop.h,0,0,w,h);
            if(enhance) {
                const pixels=ctx.getImageData(0,0,w,h),data=pixels.data;
                for(let i=0;i<data.length;i+=4){
                    const gray=.299*data[i]+.587*data[i+1]+.114*data[i+2];
                    const v=clamp((gray-128)*1.35+128,0,255);
                    data[i]=data[i+1]=data[i+2]=v;
                }
                ctx.putImageData(pixels,0,0);
            }
            return out;
        }
        function loadTesseract(){
            if(window.Tesseract?.createWorker)return Promise.resolve(window.Tesseract);
            if(!modelLoading)modelLoading=new Promise((resolve,reject)=>{
                const script=document.createElement('script');script.src=CDN;script.async=true;script.crossOrigin='anonymous';
                script.onload=()=>window.Tesseract?.createWorker?resolve(window.Tesseract):reject(new Error('Mesin OCR gagal dimuat'));
                script.onerror=()=>reject(new Error('Gagal mengunduh mesin OCR. Periksa koneksi internet.'));
                document.head.appendChild(script);
            }).catch(err=>{modelLoading=null;throw err;});
            return modelLoading;
        }
        function renderResults(data,warnings){
            latest=data;latestWarnings=warnings||[];
            const table=el('detected');table.replaceChildren();
            let filled=0;
            for(const id of IDS){
                const row=document.createElement('div');row.className='ktp-row ktp-edit-row';
                const name=document.createElement('label');name.setAttribute('for','ktp-edit-'+id);name.textContent=FIELDS[id];
                const input=document.createElement('input');input.type=id==='tgl_lhr'?'date':'text';input.id='ktp-edit-'+id;
                input.className='form-control form-control-sm';input.value=data[id]||'';
                input.placeholder='Tidak terbaca — dapat dikoreksi';input.autocomplete='off';
                row.append(name,input);table.append(row);
                if(data[id])filled++;
            }
            el('not-filled').textContent=filled+' dari '+IDS.length+' jenis informasi KTP berhasil diekstrak. Bagian kosong boleh dikoreksi; tidak semua bidang tersedia dalam form Tambah Surat.';
            el('warnings').replaceChildren();
            for(const warning of warnings||[]) {
                const node=document.createElement('li');node.textContent=warning;el('warnings').append(node);
            }
            el('results').classList.remove('d-none');
        }
        async function recognize(){
            if(reading||!image||!crop)return;
            const thisJob=jobId;
            let automaticApply=false, completed=false;
            if(crop.w<400||crop.h<230)status('Bingkai crop terlalu kecil. Perbesar bingkai untuk hasil OCR yang lebih baik.');
            busy(true);progress(2);
            el('results').classList.add('d-none');
            let worker;
            try{
                status('Memuat OCR Bahasa Indonesia / Inggris…');
                library=await loadTesseract();
                worker=await library.createWorker('ind+eng',1,{logger:m=>{
                    if(typeof m.progress==='number')progress(5+Math.round(m.progress*40));
                }});
                status('Menganalisis tulisan pada KTP (pembacaan 1 dari 2)…');
                const raw=await worker.recognize(canvasForOCR(false));
                progress(52);status('Memeriksa ulang tulisan dengan kontras berbeda (2 dari 2)…');
                const enhanced=await worker.recognize(canvasForOCR(true));
                if(thisJob!==jobId)return;
                const parsed=window.EsuketKtpParser.parseCandidates([raw?.data?.text||'',enhanced?.data?.text||'']);
                if(!Object.keys(parsed.data).length) {
                    throw new Error('Tulisan belum berhasil terbaca. Atur batas kartu pada bingkai crop, perbaiki cahaya/fokus, lalu pilih Baca Ulang.');
                }
                renderResults(parsed.data,parsed.warnings);
                completed=true;
                el('raw-text').textContent=(raw?.data?.text||'')+'\n\n--- Pembacaan kontras ---\n'+(enhanced?.data?.text||'');
                progress(90);
                if(el('image-area').classList.contains('ktp-expanded')){
                    el('image-area').classList.remove('ktp-expanded');el('enlarge').textContent='Perbesar Foto';
                }
                const reliableId=/^\d{16}$/.test(parsed.data.nik||'') && (parsed.data.name||'').length>=4;
                const identityMismatch=parsed.warnings.some(x=>/\bnik\b|\bname\b/i.test(x));
                automaticApply=Boolean(el('auto-fill').checked&&reliableId&&!identityMismatch);
                status(automaticApply
                    ? 'NIK dan nama terbaca; sedang mengisi kolom Tambah Surat otomatis…'
                    : 'Hasil OCR tersedia. '+(el('auto-fill').checked?'NIK atau nama belum meyakinkan untuk diisi otomatis. ':'')+'Periksa lalu pilih Isi ke Form.',
                    'ok');
            } catch(err) {
                status('Pembacaan gagal: '+(err?.message||'kesalahan tidak diketahui'),'error');
            } finally {
                if(worker){try{await worker.terminate();}catch(_){}}
                busy(false);
                el('progress-wrap').classList.add('d-none');
                if(!automaticApply)el('compact-progress-wrap').classList.add('d-none');
            }
            if(thisJob!==jobId)return;
            if(automaticApply){
                status('Mengisi data warga dan mencocokkan dengan master database ESUKET…');
                const report=await apply();
                progress(100);
                el('compact-progress-wrap').classList.add('d-none');
                if(thisJob!==jobId)return;
                if(report?.error){
                    expandScanner();
                    return;
                }
                // Biarkan panel OCR tersembunyi; pengguna langsung melihat form yang terisi.
                collapseScanner();
                status('Data KTP otomatis masuk ke '+(report?.count||0)+' kolom. '+
                    ((report?.missed?.length||0)+(report?.unavailable?.length||0)
                        ? 'Perlu dilengkapi dari master: '+[...(report?.missed||[]),...(report?.unavailable||[])].join(', ')+'. '
                        : '')+
                    'Periksa data dan lengkapi No. KK/pendidikan bila diperlukan. Surat belum disimpan.','ok');
                focusForm();
            } else if(completed){
                // Jangan menuliskan identitas OCR yang meragukan ke form tanpa verifikasi.
                expandScanner();
                status('Sebagian data identitas kurang meyakinkan. Koreksi hasil OCR lalu klik Isi ke Form Tambah Surat.','error');
            } else {
                expandScanner();
            }
        }
        function editedData(){
            const result={};for(const field of IDS){ const value=el('edit-'+field)?.value.trim();if(value)result[field]=value; }
            if(result.nik&&!/^\d{16}$/.test(result.nik))throw new Error('NIK harus terdiri dari tepat 16 digit angka.');
            if(result.tgl_lhr&&!/^\d{4}-\d\d-\d\d$/.test(result.tgl_lhr))throw new Error('Tanggal lahir harus YYYY-MM-DD.');
            return result;
        }
        function putText(id,text,overwrite){
            const target=document.getElementById(id);
            if(!target||!text||(!overwrite&&target.value.trim()))return false;
            target.value=id==='tgl_lhr'?text:String(text).toUpperCase();
            target.dispatchEvent(new Event('input',{bubbles:true}));target.dispatchEvent(new Event('change',{bubbles:true}));return true;
        }
        const normalize=value=>window.EsuketKtpParser.normalize(value);
        const shortRegion=value=>normalize(value).replace(/^(?:KOTAADMINISTRASI|KOTAADM|KABUPATEN|KAB|KOTA)/,'').replace(/^DKI/,'');
        function matches(item,value,id) {
            if(!item||item.id===undefined||item.id===null||!item.text)return false;
            const candidate=normalize(item.text),expected=normalize(value);
            if(id==='rt'||id==='rw'){
                const a=Number(candidate.replace(/\D/g,'')),b=Number(expected.replace(/\D/g,''));
                return Number.isFinite(a)&&Number.isFinite(b)&&a===b;
            }
            if(id==='status_kwn')return candidate===expected||candidate==='MENIKAH'&&expected==='KAWIN';
            if(id==='kabko')return shortRegion(candidate)===shortRegion(expected);
            if(id==='kewarganegaraan')return candidate===expected ||
                (expected==='WNI'&&(candidate==='WARGANEGARAINDONESIA'||candidate==='INDONESIA')) ||
                (expected==='WNA'&&(candidate==='WARGANEGARAASING'||candidate==='ASING'));
            return candidate===expected;
        }
        async function options(id,query,extra={}) {
            const url=new URL('/api/'+id,window.location.origin);
            url.searchParams.set('q',query);
            Object.entries(extra).forEach(([name,val])=>url.searchParams.set(name,val));
            const response=await fetch(url.toString(),{credentials:'same-origin',headers:{Accept:'application/json'}});
            if(!response.ok)throw new Error('Gagal mengakses master '+id+' (HTTP '+response.status+')');
            const payload=await response.json();
            return Array.isArray(payload)?payload:Array.isArray(payload?.data)?payload.data:[];
        }
        async function matchMaster(id,value,extra,overwrite){
            const select=document.getElementById(id);
            if(!select||!value||(!overwrite&&select.value))return 'skip';
            const terms=[value];
            if(id==='rt'||id==='rw')terms.push(String(parseInt(value,10)));
            if(id==='kabko')terms.push(value.replace(/^(?:KABUPATEN|KOTA(?:\s+ADMINISTRASI)?)\s+/i,''));
            if(id==='kewarganegaraan'&&value==='WNI')terms.push('INDONESIA');
            let matched=null;
            for(const q of [...new Set(terms)]){
                const rows=await options(id,q,extra);
                matched=rows.find(x=>matches(x,value,id));
                if(matched)break;
            }
            if(!matched)return 'unmatched';
            const opt=new Option(matched.text,String(matched.id),true,true);
            if(window.jQuery&&window.jQuery(select).data('select2')) {
                window.jQuery(select).empty().append(opt).trigger('change');
            } else {
                select.replaceChildren(opt);select.dispatchEvent(new Event('change',{bubbles:true}));
            }
            const hidden=document.getElementById(id+'_nm');if(hidden)hidden.value=matched.text;
            return 'filled';
        }
        async function fillExistingResident(nik,nama,overwrite) {
            // KTP tidak berisi No. KK / pendidikan. Satu-satunya sumber otomatis ialah resident ESUKET
            // dengan NIK DAN nama sama. Tidak pernah mengarang nomor KK atau kode master.
            if(!/^\d{16}$/.test(nik||'') || !(nama||'').trim())return 0;
            const requestUrl=new URL('/api/personal',window.location.origin);
            requestUrl.searchParams.set('nik',nik);
            let response;
            try {response=await fetch(requestUrl.toString(),{credentials:'same-origin',headers:{Accept:'application/json'}});}
            catch(_) {return 0;}
            if(!response.ok)return 0; // Penduduk belum tersimpan: tidak menghambat scan.
            let responseData;
            try {responseData=await response.json();}catch(_) {return 0;}
            const resident=responseData?.data?.data||responseData?.data||{};
            if(String(resident.nik||'')!==nik || normalize(resident.name||'')!==normalize(nama))return 0;
            let count=0;
            if(/^\d{16}$/.test(String(resident.kk||''))){
                if(putText('kk',String(resident.kk),overwrite))count++;
            }
            const eduId=resident.pendidikan, eduName=String(resident.pendidikan_nm||'').trim();
            const educationSelect=document.getElementById('pendidikan');
            if(educationSelect&&eduId!==undefined&&eduId!==null&&String(eduId)!==''&&eduName&&(!educationSelect.value||overwrite)) {
                try {
                    const rows=await options('pendidikan',eduName);
                    const option=rows.find(item=>String(item.id)===String(eduId)&&normalize(item.text)===normalize(eduName));
                    if(option){
                        const entry=new Option(option.text,String(option.id),true,true);
                        if(window.jQuery&&window.jQuery(educationSelect).data('select2')){
                            window.jQuery(educationSelect).empty().append(entry).trigger('change');
                        } else {
                            educationSelect.replaceChildren(entry);
                            educationSelect.dispatchEvent(new Event('change',{bubbles:true}));
                        }
                        const hidden=document.getElementById('pendidikan_nm');
                        if(hidden)hidden.value=option.text;
                        count++;
                    }
                }catch(_) {/* Jika tidak ditemukan di master, tetap kosong. */}
            }
            return count;
        }
        async function apply(){
            if(reading||!latest)return {error:'OCR belum tersedia'};
            let data;
            try{ data=editedData(); }catch(err){status(err.message,'error');return {error:err.message};}
            busy(true);
            const missed=[],unavailable=[];let count=0;
            try{
                const overwrite=el('overwrite').checked;
                for(const id of BASIC)if(putText(id,data[id],overwrite))count++;
                if(el('fill-masters').checked){
                    for(const id of DIRECT){
                        if(!data[id])continue;
                        try{
                            const matched=await matchMaster(id,data[id],{},overwrite);
                            if(matched==='filled')count++;
                            if(matched==='unmatched')missed.push(FIELDS[id]);
                        }catch(e){unavailable.push(FIELDS[id]);}
                    }
                    for(const [id,parentParam] of REGIONS){
                        if(!data[id])continue;
                        const extra={};
                        if(parentParam){
                            const parent=document.getElementById(PARENTS[parentParam]);
                            if(!parent?.value){missed.push(FIELDS[id]+' (wilayah induk belum dipilih)');continue;}
                            extra[parentParam]=parent.value;
                            if(id==='rt')extra.kode_kelurahan=document.getElementById('kelurahan')?.value||'';
                        }
                        try{
                            const matched=await matchMaster(id,data[id],extra,overwrite);
                            if(matched==='filled')count++;
                            if(matched==='unmatched')missed.push(FIELDS[id]);
                        }catch(e){unavailable.push(FIELDS[id]);}
                    }
                }
                // Isi hanya No. KK dan pendidikan dari resident bila identitas telah cocok.
                const residentCount=await fillExistingResident(data.nik,data.name,overwrite);
                count+=residentCount;
                status(count+' kolom berhasil diisi. '+
                    (missed.length?'Perlu dipilih manual dari database: '+missed.join(', ')+'. ':'')+
                    (unavailable.length?'Master gagal diakses: '+unavailable.join(', ')+'. ':'')+
                    'Periksa seluruh isi sebelum Simpan.',unavailable.length?'error':'ok');
                el('fill-note').textContent=missed.length?'Belum ditemukan dalam master data: '+missed.join(', '):'';
                lastApplyReport={count,missed,unavailable,residentCount};
                return lastApplyReport;
            }catch(err){status('Gagal mengisi form: '+err.message,'error');return {error:err.message};}
            finally{busy(false);}
        }
        el('compact-reopen').addEventListener('click',()=>{
            expandScanner();
            // Pengguna dapat mengunggah ulang, membuka kamera, atau memeriksa crop bila perlu.
            if(!image) status('Pilih kamera atau unggah foto KTP untuk memindai ulang.');
        });
        el('compact-review').addEventListener('click',()=>{
            expandScanner();
            if(latest)el('results').classList.remove('d-none');
        });
        el('open-camera').addEventListener('click',openCamera);
        function closePanel() {
            stopCamera();
            collapseScanner();
            if (!image && !latest) el('compact-bar').classList.add('d-none');
        }
        el('close-panel').addEventListener('click',closePanel);
        el('close-camera').addEventListener('click',closePanel);
        document.addEventListener('keydown',event=>{
            if(event.key==='Escape'&&!root.classList.contains('ktp-compact')&&
               !el('image-area').classList.contains('ktp-expanded')) closePanel();
        });
        el('capture').addEventListener('click',cameraCapture);
        el('refocus').addEventListener('click',async()=>{
            const success=await requestFocus(true);
            stillFrames=0;previousLuma=null;
            el('camera-focus-help').textContent=success
                ? 'Permintaan fokus dikirim ke kamera. Tahan perangkat agar stabil.'
                : 'Kontrol fokus tidak tersedia pada kamera/browser ini. Coba jauhkan sedikit atau pindah ke kamera belakang HP.';
        });
        el('auto-capture').addEventListener('change',()=>{stillFrames=0;previousLuma=null;});
        el('open-file').addEventListener('click',()=>el('file').click());
        el('file').addEventListener('change',event=>setFile(event.target.files?.[0]));
        el('reset-crop').addEventListener('click',fitCrop);
        el('new-crop').addEventListener('click',()=>{if(image){crop=null;renderStage();status('Seret pada foto dari sudut kiri atas ke kanan bawah KTP untuk membuat bingkai baru.');}});
        el('read').addEventListener('click',recognize);
        el('enlarge').addEventListener('click',()=>{
            const expanded=el('image-area').classList.toggle('ktp-expanded');
            el('enlarge').textContent=expanded?'Kecilkan Foto':'Perbesar Foto';
            if(expanded)el('image-area').scrollIntoView({block:'start'});
        });
        el('rescan').addEventListener('click',recognize);
        el('clear').addEventListener('click',()=>{jobId++;forgetImage();collapseScanner();status('Foto dihapus. Silakan pilih Scan Kamera atau Upload KTP.');});
        el('apply').addEventListener('click',apply);
        window.addEventListener('pagehide',()=>{stopCamera();forgetImage();});
    });
})();
