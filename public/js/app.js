/*
 * JavaScript general de AutoSpa.
 * Aquí va lo que se usa en varias pantallas.
 */
document.addEventListener('DOMContentLoaded', () => {

    // 1. Confirmar antes de eliminar.
    //    Cualquier <form data-confirmar="¿Seguro?"> abre la ventana de confirmación.
    const modalEl = document.getElementById('modalConfirmar');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        let formularioPendiente = null;

        document.querySelectorAll('form[data-confirmar]').forEach(form => {
            form.addEventListener('submit', evento => {
                if (form.dataset.confirmado === 'si') return; // ya se confirmó
                evento.preventDefault();
                formularioPendiente = form;
                modalEl.querySelector('.mensaje').textContent = form.dataset.confirmar;
                modal.show();
            });
        });

        modalEl.querySelector('.btn-confirmar').addEventListener('click', () => {
            if (!formularioPendiente) return;
            formularioPendiente.dataset.confirmado = 'si';
            modal.hide();
            formularioPendiente.requestSubmit();
        });
    }

    // 2. Evitar doble clic en los botones de guardar (doble registro).
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            if (form.dataset.confirmar && form.dataset.confirmado !== 'si') return;
            form.querySelectorAll('button[type=submit]').forEach(boton => {
                boton.disabled = true;
                boton.dataset.textoOriginal = boton.innerHTML;
                boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';
            });
        });
    });

    // 3. Las alertas de éxito se ocultan solas después de 6 segundos.
    document.querySelectorAll('.alerta-autocerrar').forEach(alerta => {
        setTimeout(() => bootstrap.Alert.getOrCreateInstance(alerta).close(), 6000);
    });
});

/** Formatea números como pesos: 25000 -> "$ 25.000" */
window.dinero = valor => '$ ' + Math.round(valor).toLocaleString('es-CO');
