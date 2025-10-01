<td {{ $attributes->merge(['class' => "px-6 py-4"]) }}>
    {{$slot ?? $value}}
</td>