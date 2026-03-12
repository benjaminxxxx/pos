<?php

namespace App\Livewire\Sunat\Operaciones;
use App\Models\Venta;
use App\Services\VentaServicio;
use Livewire\Component;

class RegularizarFactura extends Component
{
    public bool $mostrarFormulario = false;

    // Datos de la factura origen
    public ?int $venta_origen_id = null;
    public string $serie_origen = '';
    public string $correlativo_origen = '';
    public string $fecha_origen = '';
    public string $cdr_descripcion_origen = '';

    // Datos del formulario
    public string $nuevaFechaEmision = '';
    public string $motivoRegularizacion = '';
    public bool $esFechaAntigua = false;

    protected $rules = [
        'nuevaFechaEmision' => 'required|date',
        'motivoRegularizacion' => 'required|min:10',
    ];

    protected $listeners = ['abrirRegularizacion'];

    public function abrirRegularizacion(string $uuid): void
    {
        $venta = Venta::where('uuid', $uuid)->firstOrFail();

        $this->venta_origen_id = $venta->id;
        $this->serie_origen = $venta->serie_comprobante;
        $this->correlativo_origen = $venta->correlativo_comprobante;
        $this->fecha_origen = $venta->fecha_emision;
        $this->cdr_descripcion_origen = $venta->sunat_cdr_descripcion ?? 'Sin descripción';

        // Verificar si la fecha original excede 3 días
        $fechaOriginal = new \DateTime($venta->fecha_emision);
        $limite = (new \DateTime())->modify('-3 days');
        $this->esFechaAntigua = $fechaOriginal < $limite;

        // Precargar fecha sugerida
        $this->nuevaFechaEmision = $this->esFechaAntigua
            ? now()->format('Y-m-d')        // fecha antigua → hoy
            : $venta->fecha_emision;         // reciente → misma fecha

        // Precargar motivo sugerido
        $this->motivoRegularizacion =
            "Regularización de comprobante {$this->serie_origen}-{$this->correlativo_origen} " .
            "de fecha {$this->fecha_origen} por error de certificado digital.";

        $this->mostrarFormulario = true;
    }

    public function regularizarFactura(): void
    {
        $this->validate();

        $ventaOrigen = Venta::findOrFail($this->venta_origen_id);

        try {
            $nuevaVenta = VentaServicio::regularizar($ventaOrigen, [
                'nueva_fecha_emision' => $this->nuevaFechaEmision,
                'motivo_regularizacion' => $this->motivoRegularizacion,
            ]);

            $this->mostrarFormulario = false;
            $this->dispatch('ventaRegularizada', [
                'mensaje' => "Factura regularizada: {$nuevaVenta->serie_comprobante}-{$nuevaVenta->correlativo_comprobante}",
                'sunat_estado' => $nuevaVenta->sunat_estado,
            ]);

        } catch (\Throwable $e) {
            $this->addError('nuevaFechaEmision', $e->getMessage());
        }
    }
/*
    // Reenvío directo sin formulario
    public function reenviarSunat(string $uuid): void
    {
        $venta = Venta::where('uuid', $uuid)->firstOrFail();

        if ($venta->sunat_estado === 'aceptada') {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Ya fue aceptada por SUNAT.']);
            return;
        }

        VentaServicio::enviarASunat($venta);
        $venta->refresh();

        $this->dispatch('notify', [
            'type' => $venta->sunat_estado === 'aceptada' ? 'success' : 'error',
            'message' => $venta->sunat_estado === 'aceptada'
                ? 'Enviada y aceptada por SUNAT.'
                : "SUNAT respondió: {$venta->sunat_cdr_descripcion}",
        ]);
    }*/
    public function render()
    {
        return view('livewire.sunat.operaciones.regularizar-factura');
    }
}