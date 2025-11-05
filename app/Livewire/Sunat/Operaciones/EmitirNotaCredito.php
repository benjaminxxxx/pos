<?php
namespace App\Livewire\Sunat\Operaciones;

use App\Models\SunatCatalogo9;
use App\Models\Venta;
use App\Services\ComprobanteServicio;
use App\Services\Facturacion\Notas\NotaServicio;
use App\Services\VentaServicio;
use App\Traits\LivewireAlerta;
use Exception;
use Livewire\Component;
use Session;


class EmitirNotaCredito extends Component
{
    use LivewireAlerta;
    public $mostrarFormulario = false;
    public $catalogo9 = [];
    public $tipoNota = '01'; // Tipo de nota de crédito por defecto
    public $tipoDoc = '07'; // Tipo de documento: 07 para nota de crédito
    public $fechaEmision;
    public $serie_comprobante = '';
    public $correlativo_comprobante = '';
    public $motivo = '';
    public $sucursal_id;
    public $venta_id;
    public $tipo_comprobante_codigo;
    public $uuid;
    protected $listeners = ['generarNota'];
    public function mount()
    {
        $this->fechaEmision = now()->format('Y-m-d');
        $this->catalogo9 = SunatCatalogo9::where('estado',true)->get();
       
        $this->obtenerMotivo($this->tipoNota);
    }
    public function obtenerMotivo($codigo)
    {
        $this->motivo = $this->catalogo9->firstWhere('codigo', $codigo)->motivo ?? '';
    }
    public function updatedTipoNota($codigo)
    {
        $this->obtenerMotivo($codigo);
    }
    public function generarNota($modo, $uuid)
    {
        $venta = Venta::where('uuid', $uuid)->first();
        $this->sucursal_id = null;
        if (!$venta) {
            $this->alert('error', 'No existe la venta');
            return;
        }
        $this->sucursal_id = $venta->sucursal_id;
        if ($modo == 'anulacion') {
            $this->uuid = $uuid;
            $this->tipoNota = '01';
            $this->tipoDoc = '07';
            $this->obtenerMotivo($this->tipoNota);
            $this->serie_comprobante = $venta->serie_comprobante;
            $this->correlativo_comprobante = $venta->correlativo_comprobante;
            $this->venta_id = $venta->id;
            $this->tipo_comprobante_codigo = $venta->tipo_comprobante_codigo;
            $this->mostrarFormulario = true;
        }
    }
    public function generarNotaCredito()
    {
        try {
            $negocio_id = Session::get('negocio_seleccionado');
            if (!$negocio_id) {
                throw new Exception('Debe seleccionar un negocio');
            }


            $nota = NotaServicio::generarNotaCredito([
                'tipoDocAfectado' => $this->tipo_comprobante_codigo, // Factura
                'tipoDoc' => $this->tipoDoc,
                'numDocAfectado' => $this->serie_comprobante . '-' . $this->correlativo_comprobante,
                'tipoNota' => $this->tipoNota,
                'fechaEmision' => $this->fechaEmision,
                'motivo' => $this->motivo,
                'negocio_id' => $negocio_id,
                'sucursal_id' => $this->sucursal_id,
                'venta_id' => $this->venta_id,
            ]);

            if ($this->tipoNota == '01') { //anulacion
                
                app(VentaServicio::class)->anularVenta($this->uuid);
                /*
                Venta::find($this->venta_id)->update([
                    'estado' => 'anulado'
                ]);*/
            }


            $comprobanteServicio = new ComprobanteServicio();
            $comprobanteServicio->generarNota($nota->id);
            $this->dispatch('notaGenerada');
            $this->mostrarFormulario = false;

            
        } catch (Exception $e) {
            $this->alert('error', 'Error al generar la nota de crédito: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.sunat.operaciones.emitir-nota-credito');
    }

}