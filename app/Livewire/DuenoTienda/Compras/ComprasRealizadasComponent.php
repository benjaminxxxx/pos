<?php

namespace App\Livewire\DuenoTienda\Compras;

use App\Models\Compra;
use App\Models\ProductoEntrada;
use App\Models\Sucursal;
use App\Services\EntradaProductoServicio;
use App\Services\SalidaProductoServicio;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ComprasRealizadasComponent extends Component
{
    use WithPagination;

    // ── Filtros ──────────────────────────────────────────────────────────────
    public string $busqueda = '';
    public string $mes = '';
    public string $anio = '';
    public string $estadoPago = '';
    public string $tipoComprobante = '';

    // ── Modal confirmación anulación ─────────────────────────────────────────
    public bool $mostrarModalAnular = false;
    public ?int $compraIdAnular = null;
    public string $motivoAnulacion = '';

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'mes' => ['except' => ''],
        'anio' => ['except' => ''],
        'estadoPago' => ['except' => ''],
        'tipoComprobante' => ['except' => ''],
    ];

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }
    public function updatingMes(): void
    {
        $this->resetPage();
    }
    public function updatingAnio(): void
    {
        $this->resetPage();
    }
    public function updatingEstadoPago(): void
    {
        $this->resetPage();
    }
    public function updatingTipoComprobante(): void
    {
        $this->resetPage();
    }

    // ── Abre modal de confirmación ───────────────────────────────────────────
    public function confirmarAnulacion(int $compraId): void
    {
        $negocioUuid = session('negocio_actual_uuid');

        $sucursalIds = Sucursal::whereHas(
            'negocio',
            fn($q) =>
            $q->where('uuid', $negocioUuid)
        )->pluck('id');

        $compra = Compra::whereIn('sucursal_id', $sucursalIds)->find($compraId);

        if (!$compra) {
            $this->dispatch('notificacion', tipo: 'error', mensaje: 'Compra no encontrada.');
            return;
        }

        if ($compra->estado === false || $compra->estado_pago === 'ANULADO') {
            $this->dispatch('notificacion', tipo: 'error', mensaje: 'Esta compra ya fue anulada.');
            return;
        }

        $this->compraIdAnular = $compraId;
        $this->motivoAnulacion = '';
        $this->mostrarModalAnular = true;
    }

    public function cerrarModalAnular(): void
    {
        $this->mostrarModalAnular = false;
        $this->compraIdAnular = null;
        $this->motivoAnulacion = '';
    }

    // ── Anulación real ───────────────────────────────────────────────────────
    /**
     * Anula la compra:
     * 1. Marca la compra como ANULADO (estado_pago + estado = false).
     * 2. Por cada detalle, llama a EntradaProductoServicio::anular() para
     *    revertir el stock que aportó esa entrada — pero solo si la entrada
     *    no ha sido consumida parcialmente por ventas (el servicio ya lo
     *    protege con DomainException).
     */
    public function anularCompra(): void
    {
        if (!$this->compraIdAnular) {
            return;
        }

        $compra = Compra::with('detalles')->find($this->compraIdAnular);

        if (!$compra) {
            $this->dispatch('notificacion', tipo: 'error', mensaje: 'Compra no encontrada.');
            $this->cerrarModalAnular();
            return;
        }

        if ($compra->estado === false || $compra->estado_pago === 'ANULADO') {
            $this->dispatch('notificacion', tipo: 'error', mensaje: 'Esta compra ya fue anulada.');
            $this->cerrarModalAnular();
            return;
        }

        try {
            DB::transaction(function () use ($compra) {
                $entradaServicio = app(EntradaProductoServicio::class);

                // Revertir cada entrada vinculada a esta compra
                $entradas = ProductoEntrada::where('referencia_tipo', Compra::class)
                    ->where('referencia_id', $compra->id)
                    ->get();

                foreach ($entradas as $entrada) {
                    $entradaServicio->anular($entrada->id);
                }

                // Marcar compra como anulada (inmutable para edición, visible en historial)
                $compra->update([
                    'estado' => false,
                    'estado_pago' => 'ANULADO',
                    'glosa_o_observacion' => trim(
                        ($compra->glosa_o_observacion ?? '') . ' | ANULADO: ' . $this->motivoAnulacion
                    ),
                    'updated_by' => auth()->id(),
                ]);
            });

            $this->dispatch('notificacion', tipo: 'success', mensaje: 'Compra anulada correctamente.');

        } catch (\DomainException $e) {
            // EntradaProductoServicio::anular() lanza DomainException si hay consumo parcial
            $this->dispatch('notificacion', tipo: 'error', mensaje: $e->getMessage());
        } catch (\Throwable $e) {
            $this->dispatch('notificacion', tipo: 'error', mensaje: 'Error al anular: ' . $e->getMessage());
        }

        $this->cerrarModalAnular();
    }

    // ── Query ────────────────────────────────────────────────────────────────
    public function getComprasProperty(): LengthAwarePaginator
    {
        return Compra::query()->delNegocioActivo()
            ->with(['sucursal', 'creador'])
            ->when($this->busqueda, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('numero_comprobante', 'like', "%{$this->busqueda}%")
                        ->orWhere('proveedor_razon_social', 'like', "%{$this->busqueda}%")
                        ->orWhere('proveedor_documento_numero', 'like', "%{$this->busqueda}%");
                });
            })
            ->when($this->mes, fn($q) => $q->whereMonth('fecha_comprobante', $this->mes))
            ->when($this->anio, fn($q) => $q->whereYear('fecha_comprobante', $this->anio))
            ->when($this->estadoPago, fn($q) => $q->where('estado_pago', $this->estadoPago))
            ->when($this->tipoComprobante, fn($q) => $q->where('tipo_comprobante', $this->tipoComprobante))
            ->orderByDesc('fecha_comprobante')
            ->orderByDesc('id')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.dueno_tienda.compras.compras-realizadas-component', [
            'compras' => $this->compras,
            'anios' => range(now()->year, now()->year - 5),
        ]);
    }
}