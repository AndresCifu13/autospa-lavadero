<x-mail::message>
# 🎉 ¡Ganaste una lavada gratis!

¡Felicitaciones **{{ $cliente->nombre }}**! Completaste tu tarjeta de fidelización y tu próxima lavada **no tiene costo**.

<x-mail::panel>
🎁 Lavadas gratis disponibles: **{{ $cliente->lavadas_gratis }}**
</x-mail::panel>

**¿Cómo la uso?** La próxima vez que vengas, dile al empleado que quieres usar tu lavada gratis. Nosotros nos encargamos del resto.

<span class="etiqueta etiqueta-lila">Cliente frecuente ⭐</span>

Gracias por preferirnos,<br>
**{{ config('app.name') }}**
</x-mail::message>
