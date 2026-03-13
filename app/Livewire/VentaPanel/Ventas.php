<?php

namespace App\Livewire\VentaPanel;

use App\Models\Venta;
use App\Services\Ventas\InformacionVenta;
use App\Services\VentaServicio;
use App\Traits\ConNegocioSeleccionado;
use App\Traits\DatosUtiles\ConSucursales;
use App\Traits\LivewireAlerta;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Ventas extends Component
{
    use WithPagination, WithoutUrlPagination, LivewireAlerta, ConSucursales, ConNegocioSeleccionado;
    public $filtroSucursal;
    public $filtroCliente = '';
    public $filtroDesde;
    public $filtroHasta;
    protected $listeners = ['notaGenerada', 'ventaRegularizada'];
    public function mount()
    {
        $this->cargarNegocioSeleccionado();
    }
    public function updatedFiltroCliente()
    {
        $this->resetPage();
    }
    public function ventaRegularizada($data)
    {
        $this->alertModal(
            $data['sunat_estado'] === 'aceptada' ? 'success' : 'warning',
            'Respuesta de SUNAT',
            $data['sunat_estado'] === 'aceptada'
            ? $data['mensaje']
            : "SUNAT respondió: {$data['mensaje']}"
        );
        $this->resetPage();
    }
    public function revalidarVenta($uuid)
    {
        try {
            app(VentaServicio::class)->revalidarVenta($uuid);
            $this->alert('success', 'Venta Revalidada con éxito.');
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
    public function anularVenta($uuid)
    {
        try {
            app(VentaServicio::class)->anularVenta($uuid);
            $this->alert('success', 'Venta Anulada Correctamente.');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function reenviarSunat(string $uuid): void
    {
        $venta = Venta::where('uuid', $uuid)->firstOrFail();

        if ($venta->sunat_estado === 'aceptada') {
            $this->alert('success', 'Ya fue aceptada por SUNAT.');
            return;
        }

        try {
            VentaServicio::enviarASunat($venta);
            $venta->refresh();

            $this->alertModal(
                $venta->sunat_estado === 'aceptada' ? 'success' : 'warning',
                'Respuesta de SUNAT',
                $venta->sunat_estado === 'aceptada'
                ? 'Enviada y aceptada por SUNAT.'
                : "SUNAT respondió: {$venta->sunat_cdr_descripcion}"
            );
        } catch (\Exception $e) {
            $this->alertModal(
                'error',
                'Respuesta de SUNAT',
                $e->getMessage()
            );
        }
    }
    public function notaGenerada()
    {
        $this->alert('success', 'Nota de crédito generada correctamente.');
    }
    public function render()
    {
        $filtros = [
            'sucursal_id' => $this->filtroSucursal,
            'nombre_cliente' => $this->filtroCliente,
            'fecha_desde' => $this->filtroDesde,
            'fecha_hasta' => $this->filtroHasta,
        ];
        $ventas = InformacionVenta::listarVentas($filtros);
        return view(
            'livewire.venta_panel.ventas',
            [
                'ventas' => $ventas,
            ]
        );
    }
}

