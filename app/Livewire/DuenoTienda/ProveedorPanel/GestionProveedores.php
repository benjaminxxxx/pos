<?php

namespace App\Livewire\DuenoTienda\ProveedorPanel;

use App\Services\ProveedorServicio;
use App\Traits\LivewireAlerta;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class GestionProveedores extends Component
{
    use WithPagination, LivewireAlerta, WithoutUrlPagination;

    public int $cuentaId;
    public string $filtroNombre = '';
    public string $filtroEstado = '';
    public string $filtroEliminados = '';

    protected $listeners = ['proveedorGuardado' => '$refresh'];
    private ProveedorServicio $proveedorServicio;

    public function boot(ProveedorServicio $proveedorServicio)
    {
        $this->proveedorServicio = $proveedorServicio;
    }

    public function mount()
    {
        $this->cuentaId = auth()->user()->cuenta->id;
    }

    public function eliminarProveedor(string $uuid)
    {
        try {
            $fueEliminado = $this->proveedorServicio->eliminar($uuid, $this->cuentaId);
            if ($fueEliminado) {
                $this->alert('success', 'Proveedor eliminado correctamente.');
            } else {
                $this->alert('warning', 'No se encontró el proveedor para eliminar.');
            }
        } catch (\Throwable $th) {
            Log::error('Error al eliminar proveedor: ' . $th->getMessage());
            $this->alert('error', 'Ocurrió un error al eliminar el proveedor.');
        }
    }

    public function restaurarProveedor(string $uuid)
    {
        try {
            $fueRestaurado = $this->proveedorServicio->restaurar($uuid, $this->cuentaId);
            if ($fueRestaurado) {
                $this->alert('success', 'Proveedor restaurado correctamente.');
            } else {
                $this->alert('warning', 'No se pudo restaurar el proveedor.');
            }
        } catch (\Throwable $th) {
            Log::error('Error al restaurar proveedor: ' . $th->getMessage());
            $this->alert('error', 'Ocurrió un error al restaurar el proveedor.');
        }
    }

    public function render()
    {
        $proveedores = $this->proveedorServicio->listarPaginado(
            $this->cuentaId,
            $this->filtroNombre,
            $this->filtroEstado,
            $this->filtroEliminados
        );

        return view('livewire.dueno_tienda.proveedores_panel.gestion-proveedores', [
            'proveedores' => $proveedores
        ]);
    }
}