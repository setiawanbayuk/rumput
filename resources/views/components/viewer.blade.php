@props([
    'src',
    'height' => '230px',
    'id' => 'viewer_' . uniqid()
])

{{-- Thumbnail --}}
<img src="{{ asset($src) }}"
        alt="Preview"
        class="rounded"
        style="max-height: {{ $height }}; cursor: pointer;"
        data-bs-toggle="modal"
        data-bs-target="#{{ $id }}">

@push('modals')
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-4 overflow-hidden border-0 shadow-lg">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center px-2 py-2"
                    style="background: #7590A8; color: white;">
                <h5 class="m-0 fw-semibold text-center flex-grow-1"
                    style="font-size: 1.1rem; letter-spacing: .5px;">
                    Pratinjau Gambar
                </h5>
                <button type="button" class="btn p-0 me-2"
                        data-bs-dismiss="modal"
                        style="color:white; font-size: 1.4rem;">
                    ×
                </button>
            </div>
            {{-- BODY --}}
            <div class="modal-body p-0 text-center position-relative"
                    style="background: #ffffff; overflow: hidden;">
                {{-- Image --}}
                <img id="{{ $id }}_img"
                        src="{{ asset($src) }}"
                        class="img-fluid"
                        alt="Image Preview"
                        style="max-height: 85vh; transition: transform .25s ease; object-fit: contain;">
            </div>
            {{-- ZOOM CONTROLS --}}
            <div class="position-absolute bottom-0 end-0 m-3 d-flex gap-2"
                    style="z-index: 10;">
                <button class="btn btn-light rounded-circle shadow-sm"
                        onclick="zoomIn('{{ $id }}_img')">
                    <i class="ri-zoom-in-line"></i>
                </button>
                <button class="btn btn-light rounded-circle shadow-sm"
                        onclick="zoomOut('{{ $id }}_img')">
                    <i class="ri-zoom-out-line"></i>
                </button>
                <button class="btn btn-light rounded-circle shadow-sm"
                        onclick="resetZoom('{{ $id }}_img')">
                    <i class="ri-refresh-line"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    let zoomLevels = {};
    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let currentX = 0;
    let currentY = 0;

    function enableDrag(img) {
        img.addEventListener("mousedown", function (e) {

            // drag hanya aktif jika zoom > 1
            if ((zoomLevels[img.id] || 1) <= 1) {
                img.style.cursor = "default";
                return;
            }

            isDragging = true;

            startX = e.clientX - currentX;
            startY = e.clientY - currentY;

            img.style.cursor = "grabbing";
        });

        document.addEventListener("mouseup", function () {
            isDragging = false;
            img.style.cursor = (zoomLevels[img.id] || 1) > 1 ? "grab" : "default";
        });

        document.addEventListener("mousemove", function (e) {
            if (!isDragging) return;

            currentX = e.clientX - startX;
            currentY = e.clientY - startY;

            img.style.transform = `
                translate(${currentX}px, ${currentY}px)
                scale(${zoomLevels[img.id] || 1})
            `;
        });
    }

    function zoomIn(id) {
        zoomLevels[id] = (zoomLevels[id] || 1) + 0.2;

        const img = document.getElementById(id);
        enableDrag(img);

        img.style.cursor = "grab";
        img.style.transform = `translate(${currentX}px, ${currentY}px) scale(${zoomLevels[id]})`;
    }

    function zoomOut(id) {
        zoomLevels[id] = Math.max(1, (zoomLevels[id] || 1) - 0.2);

        const img = document.getElementById(id);

        if (zoomLevels[id] <= 1) {
            // reset posisi & disable drag
            currentX = 0;
            currentY = 0;
            img.style.cursor = "default";
            img.style.transform = "scale(1)";
            return;
        }

        img.style.cursor = "grab";
        img.style.transform = `translate(${currentX}px, ${currentY}px) scale(${zoomLevels[id]})`;
    }

    function resetZoom(id) {
        zoomLevels[id] = 1;

        currentX = 0;
        currentY = 0;

        const img = document.getElementById(id);
        img.style.cursor = "default";
        img.style.transform = "scale(1)";
    }
</script>
@endpush
