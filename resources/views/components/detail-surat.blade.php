<div class="card mt-4 border-0" 
    style="background-color: rgba(174,160,122,.25); position: relative; border-radius: 12px; padding: .5rem; padding-bottom: 4rem;">
    <h5 class="card-title pt-3 text-center"
        style="color: rgb(174,160,122); font-size: 1.3rem; font-weight:700; letter-spacing:1px;">{{ $title }}</h5>
    <div class="card-body">
        <div class="card mb-4 p-2 shadow-sm rounded-4">
            <h5 class="card-title pt-3 text-center text-black"
                style="font-size: 1.3rem; font-weight:700; letter-spacing: 1px">DESKRIPSI</h5>
            <div class="card-body">
                <p class="card-text">
                    {{ $detail }}
                </p>

            </div>
        </div>
        <div class="card mb-3 p-2 shadow-sm rounded-4">
            <h5 class="card-title pt-3 text-center text-black"
                style="font-size: 1.3rem; font-weight:700; letter-spacing: 1px">PERSYARATAN</h5>
            <div class="card-body">
                <p class="card-text">
                    {{ $persyaratan }}
                </p>
            </div>
        </div>
    </div>
</div>
