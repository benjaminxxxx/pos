<th {{ $attributes->merge(['class' => "px-6 py-3 text-xs font-medium bg-zinc-200 dark:bg-zinc-950 uppercase tracking-wider",'scope'=>"col"]) }}>
    {{$slot ?? $value}}
</th>