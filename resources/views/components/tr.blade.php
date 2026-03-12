<tr {{ $attributes->merge(['class' => "bg-muted border-b border-border"]) }}>
    {{$slot ?? $value}}
</tr>