@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<span class="marca-icono">🚗</span>{!! $slot !!}
</a>
</td>
</tr>
