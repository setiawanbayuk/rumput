<div class="card p-2" style="background-color: rgba(174,160,122,.25)">
    <h5 class="card-title text-center" style="color: rgb(174,160,122)">{{ $title }}</h5>
    <div class="card-body">
        <div class="card mb-4 p-2">
            <h5 class="card-title text-center" style="color: rgb(174,160,122)">Deskripsi</h5>
            <div class="card-body">
                <p class="card-text">
                    {{$detail}}
                </p>

            </div>
        </div>
        <div class="card mb-4 p-2">
            <h5 class="card-title text-center" style="color: rgb(174,160,122)">Persyaratan</h5>
            <div class="card-body">
                <p class="card-text">
                    {{$persyaratan}}
                </p>

            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn text-white mb-3 py-2 w-75"
                style="background: linear-gradient(to right, #c19a6b, #b08d57); font-size: 14px; border-radius: 6px;"
                data-bs-toggle="modal" data-bs-target="#modalTambah">
                Ajukan
            </button>

        </div>
    </div>
</div>