/*
 * JavaScript general de AutoSpa.
 * Aquí va lo que se usa en varias pantallas.
 */
document.addEventListener('DOMContentLoaded', () => {

    // 1. Confirmar antes de eliminar y 2. evitar doble clic al guardar.
    //    Se escucha en todo el documento (delegación) para que también funcione
    //    con los resultados que llegan con la búsqueda en vivo.
    const modalEl = document.getElementById('modalConfirmar');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    let formularioPendiente = null;

    document.addEventListener('submit', evento => {
        const form = evento.target;
        if (form.matches('[data-busqueda-vivo]')) return; // lo maneja la búsqueda en vivo

        // Cualquier <form data-confirmar="¿Seguro?"> abre la ventana de confirmación.
        if (modal && form.dataset.confirmar && form.dataset.confirmado !== 'si') {
            evento.preventDefault();
            formularioPendiente = form;
            modalEl.querySelector('.mensaje').textContent = form.dataset.confirmar;
            modal.show();
            return;
        }

        form.querySelectorAll('button[type=submit]').forEach(boton => {
            boton.disabled = true;
            boton.dataset.textoOriginal = boton.innerHTML;
            boton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';
        });
    });

    modalEl?.querySelector('.btn-confirmar').addEventListener('click', () => {
        if (!formularioPendiente) return;
        formularioPendiente.dataset.confirmado = 'si';
        modal.hide();
        formularioPendiente.requestSubmit();
    });

    // 3. Las alertas de éxito se ocultan solas después de 6 segundos.
    document.querySelectorAll('.alerta-autocerrar').forEach(alerta => {
        setTimeout(() => bootstrap.Alert.getOrCreateInstance(alerta).close(), 6000);
    });

    // 4. Búsqueda en vivo.
    //    <form data-busqueda-vivo> + un contenedor [data-resultados] en la página.
    //    Los resultados se actualizan mientras se escribe (desde la primera letra)
    //    y al presionar Enter, sin recargar la página.
    document.querySelectorAll('form[data-busqueda-vivo]').forEach(form => {
        let espera = null;
        let peticion = null;

        const buscar = async () => {
            const contenedor = document.querySelector('[data-resultados]');
            if (!contenedor) return form.submit();

            const parametros = new URLSearchParams();
            for (const [clave, valor] of new FormData(form)) {
                if (String(valor).trim() !== '') parametros.append(clave, valor);
            }
            const url = (form.getAttribute('action') || location.pathname)
                + (parametros.toString() ? '?' + parametros : '');

            peticion?.abort();
            peticion = new AbortController();
            contenedor.classList.add('buscando');

            try {
                const respuesta = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: peticion.signal,
                });
                const html = new DOMParser().parseFromString(await respuesta.text(), 'text/html');
                const nuevos = html.querySelector('[data-resultados]');
                if (nuevos) contenedor.innerHTML = nuevos.innerHTML;
                history.replaceState(null, '', url);
            } catch (error) {
                if (error.name !== 'AbortError') form.submit(); // si falla, búsqueda normal
            } finally {
                contenedor.classList.remove('buscando');
            }
        };

        form.addEventListener('input', evento => {
            if (evento.target.type === 'checkbox' || evento.target.tagName === 'SELECT') return;
            clearTimeout(espera);
            espera = setTimeout(buscar, 250);
        });
        form.addEventListener('change', evento => {
            if (evento.target.type === 'checkbox' || evento.target.tagName === 'SELECT') buscar();
        });
        form.addEventListener('submit', evento => { // Enter: buscar de una
            evento.preventDefault();
            clearTimeout(espera);
            buscar();
        });
    });
});

/** Formatea números como pesos: 25000 -> "$ 25.000" */
window.dinero = valor => '$ ' + Math.round(valor).toLocaleString('es-CO');
