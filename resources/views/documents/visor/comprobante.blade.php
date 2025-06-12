@extends('layouts.app')

@section('content')
    @if ($pdf)
        <iframe src="{{ Storage::disk('public')->url($pdf) }}"
            style="position: absolute; top: 0; left: 0; bottom: 0; right: 0; width: 100%; height: 100%; border: none;"></iframe>
    @else
        <div
            style="
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9fafb;
            color: #333;
            font-family: sans-serif;
            font-size: 1.5rem;
            text-align: center;
            padding: 2rem;
        ">
            No se encontró el comprobante.<br>
            Verifique la serie y el número ingresado.
        </div>
    @endif
@endsection
