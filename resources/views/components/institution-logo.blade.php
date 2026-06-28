@php
    $institutions = config('institutions');
    $all = array_merge($institutions['banks'], $institutions['ewallets'], $institutions['cash']);
    $inst = $all[$slug] ?? null;
    $size = $size ?? 40;
    $fontSize = $size * 0.35;

    if (!$inst) {
        $fallbackName = $fallbackName ?? '?';
        $fallbackColor = $fallbackColor ?? '#6B7280';
        $monogram = strlen($fallbackName) > 2 ? substr($fallbackName, 0, 2) : $fallbackName;
    }
@endphp

@if ($inst)
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => 'shrink-0']) }}>
        <rect width="40" height="40" rx="8" fill="{{ $inst['color'] }}"/>
        <text x="20" y="27" text-anchor="middle" fill="#fff" font-size="{{ $fontSize }}" font-weight="bold" font-family="Arial, sans-serif">{{ $inst['monogram'] }}</text>
    </svg>
@else
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => 'shrink-0']) }}>
        <rect width="40" height="40" rx="8" fill="{{ $fallbackColor }}"/>
        <text x="20" y="27" text-anchor="middle" fill="#fff" font-size="{{ $fontSize }}" font-weight="bold" font-family="Arial, sans-serif">{{ $monogram }}</text>
    </svg>
@endif
