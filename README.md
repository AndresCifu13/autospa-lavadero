# AutoSpa M&A — Sistema del lavadero

Aplicación web (adaptada a celular) para el **Lavadero AutoSpa M&A**. Reemplaza el proceso manual de:
registrar vehículos, cobrar, llevar la tarjeta de fidelización (cada X lavadas, una gratis),
vender en la tienda, registrar gastos y sacar reportes diarios y mensuales.

Hecho con **Laravel 12 + PHP 8.2**, Blade y Bootstrap 5 (colores pastel).

---

## 1. Requisitos

| Programa | Versión | Nota |
|---|---|---|
| PHP | **8.2 o superior** | El que trae XAMPP (8.2.12) sirve |
| Composer | 2.x | https://getcomposer.org |
| MySQL / MariaDB | 8 / 10.6+ | El que viene con XAMPP o Laragon sirve |

> No necesitas Node ni `npm`: los estilos (Bootstrap e íconos) se cargan desde internet (CDN).

## 2. Instalación paso a paso

```bash
# 1) Entra a la carpeta del proyecto
cd autospa   # la carpeta donde está composer.json

# 2) Instala las librerías de PHP
composer install

# 3) Crea el archivo de configuración
copy .env.example .env        # en Windows
# cp .env.example .env        # en Mac / Linux

# 4) Genera la llave de la aplicación
php artisan key:generate
```

5) Crea en phpMyAdmin una base de datos llamada **`autospa`** (cotejamiento `utf8mb4_unicode_ci`).
   Si tu MySQL tiene contraseña, escríbela en `DB_PASSWORD` dentro del `.env`.

```bash
# 6) Crea las tablas y llena datos de ejemplo
php artisan migrate --seed

# 7) Arranca el servidor
php artisan serve
```

Abre **http://localhost:8000**.
Para verlo en el celular (misma red WiFi): `php artisan serve --host=0.0.0.0` y entra con la IP del computador, ej. `http://192.168.1.10:8000`.

### Usuarios de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | admin@autospa.com | `Admin123*` |
| Empleado | empleado@autospa.com | `Empleado123*` |

Para borrar todo y volver a empezar: `php artisan migrate:fresh --seed`

---

## 3. ¿Qué puede hacer cada rol?

| Función | Administrador | Empleado |
|---|:-:|:-:|
| Registrar vehículos, ver los del día, avanzar estado, cobrar | ✅ | ✅ (solo avanza sus vehículos o los sin asignar) |
| Clientes y vehículos: crear / editar | ✅ | ✅ |
| Clientes, vehículos y lavados: **eliminar** | ✅ | ❌ |
| Tienda: hacer ventas, ver productos | ✅ | ✅ |
| Tienda: crear/editar productos, **anular ventas** | ✅ | ❌ |
| Trabajadores (CRUD) | ✅ | ❌ |
| Servicios y precios | ✅ | ❌ |
| Gastos | ✅ | ❌ |
| Reportes diario / mensual y envío por correo | ✅ | ❌ |
| Configuración (lavadas para la gratis, correo de reportes) | ✅ | ❌ |

Los permisos se controlan en dos lugares:
- `app/Http/Middleware/VerificarRol.php` → bloquea secciones completas (`->middleware('rol:admin')` en `routes/web.php`).
- `app/Policies/*` → reglas por registro (ej: un empleado no puede mover el vehículo asignado a otro).

---

## 4. Historias de usuario → dónde está el código

| HU | Descripción | Archivo principal |
|---|---|---|
| HU-00 a HU-04 | Gestión de personal (ver, agregar, editar, eliminar) | `TrabajadorController`, `UsuarioService` |
| HU-05, HU-06 | Registro de vehículos / agregar vehículo | `LavadoController@store`, `LavadoService::registrar()` |
| HU-07 | Servicios según tipo de vehículo | `Servicio::precioPara()`, `ServicioController` |
| HU-08 | Calcular precio total | `PrecioService` (también se ve en vivo en el formulario) |
| HU-09, HU-11 | Control de pagos / marcar como pagado | `LavadoService::registrarPago()` |
| HU-10 | Lista de vehículos del día | `LavadoController@index` |
| HU-12 | Eliminar vehículo | `LavadoService::eliminar()` |
| HU-13, HU-14 | Finanzas / registrar gastos | `GastoController` |
| HU-15 | Reporte diario | `ReporteService::diario()` |
| HU-16 | Enviar reporte por correo | `ReporteService::enviarPorCorreo()`, comando `reportes:enviar-diario` |
| HU-17 | Reporte mensual | `ReporteService::mensual()` |
| Extra | Fidelización (lavada gratis) | `FidelizacionService` |
| Extra | Tienda (productos y ventas) | `ProductoController`, `VentaService` |

---

## 5. Cómo está organizado (POO)

```
app/
├── Enums/            Valores fijos: Rol, TipoVehiculo, EstadoLavado, MetodoPago, CategoriaGasto
├── Models/           Tablas de la base de datos (Eloquent) + relaciones y filtros (scopes)
├── Services/         ⭐ La lógica del negocio. Aquí están las reglas del lavadero
├── Http/
│   ├── Controllers/  Reciben la petición, llaman al servicio y devuelven la vista (delgados)
│   ├── Requests/     Validación de cada formulario
│   └── Middleware/   VerificarRol, UsuarioActivo
├── Policies/         Permisos por registro
├── Exceptions/       Errores del negocio (NegocioException y sus hijas)
├── Notifications/    Correos: bienvenida, vehículo listo, lavada gratis, stock bajo
├── Mail/             Correo del reporte
└── DTO/              ResumenReporte (objeto de solo lectura con los números del reporte)
```

Ideas de POO que se usan:
- **Responsabilidad única**: el controlador no calcula precios; eso lo hace `PrecioService`.
- **Inyección de dependencias**: los servicios se reciben en el constructor y Laravel los crea solos.
- **Herencia**: `StockInsuficienteException` y `LavadoException` heredan de `NegocioException`.
- **Encapsulamiento**: `EstadoLavado::siguiente()` sabe cuál es el siguiente estado; nadie más lo decide.
- **Enums con métodos**: cada tipo de vehículo sabe su etiqueta, su ícono y su columna de precio.
- **Transacciones**: si algo falla al registrar una venta o un lavado, no queda nada guardado a medias.

## 6. Manejo de errores

- **Errores de formulario**: los `Requests` validan y la vista muestra el mensaje en español debajo de cada campo.
- **Errores del negocio** (sin stock, pagar dos veces, entregar sin pagar, usar una gratis que no tiene):
  se lanzan como `NegocioException` y el usuario ve una alerta rosada. No hace falta try/catch en los controladores.
- **Correo caído**: `NotificadorService` atrapa el error, lo guarda en `storage/logs/laravel.log` y la app sigue funcionando.
- **Base de datos** (con `APP_DEBUG=false`): mensaje amable en lugar de la pantalla de error.
- **Páginas de error propias**: 403, 404, 419, 429, 500 y 503 en `resources/views/errors`.
- **Inicio de sesión**: bloqueo de 1 minuto tras 5 intentos fallidos; usuarios desactivados no pueden entrar.

## 7. Correos electrónicos

Por defecto `MAIL_MAILER=log`: los correos **no se envían**, se escriben en `storage/logs/laravel.log` (sirve para probar).

Para enviarlos de verdad con Gmail:
1. En tu cuenta de Google activa la verificación en dos pasos.
2. Crea una **contraseña de aplicación** (Cuenta de Google → Seguridad → Contraseñas de aplicaciones).
3. En el `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD="xxxx xxxx xxxx xxxx"
MAIL_FROM_ADDRESS=tu_correo@gmail.com
```

4. `php artisan config:clear`

Correos que envía el sistema:
- **Bienvenida** al crear un trabajador (con su contraseña).
- **Vehículo listo** al cliente cuando el lavado pasa a "Terminado".
- **¡Ganaste una lavada gratis!** al cliente cuando completa su tarjeta.
- **Stock bajo** al administrador cuando un producto llega al mínimo.
- **Reporte diario/mensual** (botón en Reportes o automático).

### Reporte automático cada noche

En `routes/console.php` está programado para las 8:00 p.m. Para que corra, deja abierta otra terminal con:

```bash
php artisan schedule:work
```

O envíalo a mano: `php artisan reportes:enviar-diario` (o `--fecha=2026-10-19`).

## 8. Pruebas automáticas

```bash
php artisan test
```

Hay 48 pruebas en `tests/Feature` que revisan: inicio de sesión, permisos por rol, cálculo de precios,
flujo de estados y pagos, la lavada gratis, la tienda (stock), los reportes y que todas las pantallas carguen.
Usan una base de datos SQLite en memoria, así que **no tocan tu base de datos**.

## 9. Ideas para los siguientes sprints

- Recuperar contraseña por correo.
- Subir foto del vehículo al llegar (evidencia de rayones).
- Exportar los reportes a PDF o Excel.
- Página para que el cliente consulte el estado de su vehículo con la placa.
