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
        if (evento.defaultPrevented) return; // otra parte de la página canceló el envío
        if (form.matches('[data-busqueda-vivo]')) return; // lo maneja la búsqueda en vivo

        // Cualquier <form data-confirmar="¿Seguro?"> abre la ventana de confirmación.
        if (modal && form.dataset.confirmar && form.dataset.confirmado !== 'si') {
            evento.preventDefault();
            formularioPendiente = form;
            modalEl.querySelector('.mensaje').textContent = form.dataset.confirmar;
            modal.show();
            return;
        }

        // Evita el doble envío y muestra el spinner en el botón presionado
        if (form.dataset.enviando === 'si') {
            evento.preventDefault();
            return;
        }
        form.dataset.enviando = 'si';
        const boton = evento.submitter
            || form.querySelector('button[type=submit], button:not([type]), input[type=submit]');
        if (boton) mostrarCargando(boton);
        mostrarBarraCarga();
    });

    // 5. Spinner en los enlaces con forma de botón (Nuevo, Ver, Editar, paginación, menús).
    document.addEventListener('click', evento => {
        const enlace = evento.target.closest('a.btn, a.page-link, a.dropdown-item, a.nav-link');
        if (!enlace || !esNavegacion(enlace, evento)) return;
        mostrarCargando(enlace);
        mostrarBarraCarga();
    });

    // Cualquier otra navegación (menú, formularios enviados con JS): barra superior
    window.addEventListener('beforeunload', mostrarBarraCarga);

    // Al volver con el botón "Atrás", el navegador muestra la página guardada:
    // quitamos los spinners para que los botones vuelvan a funcionar.
    window.addEventListener('pageshow', restaurarCargando);

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
            const controlador = new AbortController();
            peticion = controlador;
            contenedor.classList.add('buscando');
            form.classList.add('buscando');

            try {
                const respuesta = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: controlador.signal,
                });
                const html = new DOMParser().parseFromString(await respuesta.text(), 'text/html');
                const nuevos = html.querySelector('[data-resultados]');
                if (nuevos) contenedor.innerHTML = nuevos.innerHTML;
                history.replaceState(null, '', url);
            } catch (error) {
                if (error.name !== 'AbortError') form.submit(); // si falla, búsqueda normal
            } finally {
                if (peticion === controlador) { // solo la búsqueda más reciente
                    contenedor.classList.remove('buscando');
                    form.classList.remove('buscando');
                }
            }
        };

        // Spinner pequeño dentro de la caja de búsqueda
        const caja = form.querySelector('input[name=buscar]');
        if (caja) {
            caja.parentElement.classList.add('caja-busqueda');
            caja.insertAdjacentHTML('afterend', '<span class="spinner-border spinner-border-sm spinner-busqueda" role="status" aria-hidden="true"></span>');
        }

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

/* ---------- Indicadores de carga ---------- */

const SPINNER = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

/** Pone un spinner en el botón o enlace (al lado del texto) y lo bloquea. */
function mostrarCargando(elemento) {
    if (elemento.dataset.cargando === 'si') return;
    elemento.dataset.cargando = 'si';
    elemento.dataset.htmlOriginal = elemento.innerHTML;
    elemento.setAttribute('aria-busy', 'true');
    elemento.classList.add('cargando');

    // Si tiene texto, el spinner reemplaza al icono y queda al lado del texto.
    // Si solo tiene icono (ej: el ojo de "ver"), el spinner ocupa su lugar.
    const tieneTexto = elemento.textContent.trim() !== '';
    const icono = elemento.querySelector('i.bi');
    if (tieneTexto && icono) {
        icono.outerHTML = SPINNER.replace('class="', 'class="me-1 ');
    } else if (tieneTexto) {
        elemento.insertAdjacentHTML('afterbegin', SPINNER.replace('class="', 'class="me-1 '));
    } else {
        elemento.innerHTML = SPINNER;
    }

    // Se desactiva un instante después para no cancelar el envío del formulario
    if (elemento.tagName === 'BUTTON' || elemento.tagName === 'INPUT') {
        setTimeout(() => { elemento.disabled = true; }, 0);
    }
}

/** Devuelve todos los botones y formularios a su estado normal. */
function restaurarCargando() {
    document.querySelectorAll('[data-cargando="si"]').forEach(elemento => {
        elemento.innerHTML = elemento.dataset.htmlOriginal;
        elemento.disabled = false;
        elemento.classList.remove('cargando');
        elemento.removeAttribute('aria-busy');
        delete elemento.dataset.cargando;
    });
    document.querySelectorAll('form[data-enviando]').forEach(form => delete form.dataset.enviando);
    document.getElementById('barraCarga')?.classList.remove('activa');
}

/** Barra delgada arriba de la pantalla mientras carga la siguiente página. */
function mostrarBarraCarga() {
    let barra = document.getElementById('barraCarga');
    if (!barra) {
        barra = document.createElement('div');
        barra.id = 'barraCarga';
        barra.setAttribute('role', 'progressbar');
        barra.setAttribute('aria-label', 'Cargando');
        document.body.prepend(barra);
    }
    barra.classList.remove('activa');
    void barra.offsetWidth; // reinicia la animación
    barra.classList.add('activa');
}

/** ¿Este clic va a cargar otra página en la misma pestaña? */
function esNavegacion(enlace, evento) {
    const href = enlace.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:')) return false;
    if (enlace.target && enlace.target !== '_self') return false;
    if (enlace.hasAttribute('download') || enlace.dataset.bsToggle || enlace.dataset.sinSpinner !== undefined) return false;
    if (evento.defaultPrevented || evento.button !== 0 || evento.ctrlKey || evento.metaKey || evento.shiftKey || evento.altKey) return false;
    const destino = new URL(enlace.href, location.href);
    if (destino.origin !== location.origin) return false;
    // Mismo enlace solo con #ancla: no recarga
    if (destino.pathname === location.pathname && destino.search === location.search && destino.hash) return false;
    return true;
}

/** Formatea números como pesos: 25000 -> "$ 25.000" */
window.dinero = valor => '$ ' + Math.round(valor).toLocaleString('es-CO');
