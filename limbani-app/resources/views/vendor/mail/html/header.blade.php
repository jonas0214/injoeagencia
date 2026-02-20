@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@php
    // Intentamos obtener el usuario desde la variable $user (si se pasa a la vista)
    // o desde el usuario autenticado (si la notificación es síncrona).
    $displayUser = $user ?? auth()->user();
    $photoUrl = null;
    if ($displayUser && $displayUser->teamMember && $displayUser->teamMember->photo) {
        $photoUrl = config('app.url') . '/storage/' . $displayUser->teamMember->photo;
    }
@endphp

@if ($photoUrl)
    <img src="{{ $photoUrl }}" class="logo" alt="Colaborador" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
@elseif (trim($slot) === 'Laravel' || trim($slot) === 'Limbani')
    <img src="{{ config('app.url') }}/logo-injoe.png" class="logo" alt="Logo" style="width: auto; height: 60px;">
@else
    {!! $slot !!}
@endif
</a>
</td>
</tr>
