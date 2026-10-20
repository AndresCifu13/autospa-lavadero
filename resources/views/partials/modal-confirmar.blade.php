{{-- Ventana para confirmar antes de eliminar (la controla public/js/app.js) --}}
<div class="modal fade" id="modalConfirmar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content tarjeta border-0">
            <div class="modal-body text-center p-4">
                <div class="etiqueta pastel-rosa fs-3 p-3 mb-3"><i class="bi bi-trash3"></i></div>
                <h5 class="mb-2">¿Estás seguro?</h5>
                <p class="mensaje text-suave mb-4"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-blanco" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-rosa btn-confirmar">Sí, continuar</button>
                </div>
            </div>
        </div>
    </div>
</div>
