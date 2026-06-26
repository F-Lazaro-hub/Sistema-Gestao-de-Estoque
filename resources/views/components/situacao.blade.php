@props(['value'])

@php
    $labels = [
        'pendente'  => 'Pendente',
        'aprovada'  => 'Aprovada',
        'reprovada' => 'Reprovada',
        'cancelada' => 'Cancelada',
        'pendente'    => 'Aberto',
        'resolvido' => 'Resolvido',
        'entrada'   => 'Entrada',
        'saida'     => 'Saída',
        'ajuste'    => 'Ajuste',
    ];

    $label = $labels[$value] ?? ucfirst($value ?? '—');
    $class = 'badge-' . ($value ?? 'inativo');
@endphp

<span {{ $attributes->merge(['class' => "badge-situacao $class"]) }}>{{ $label }}</span>
