<?php

namespace App\Livewire\DuenoTienda\MovimientoPanel;

use App\Models\TipoMovimiento;
use App\Services\Caja\MovimientoCajaServicio;
use App\Traits\DatosUtiles\ConSucursales;
use App\Traits\LivewireAlerta;
use Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MovimientoFormComponent extends Component
{
    use WithFileUploads;
    use LivewireAlerta;
    use ConSucursales;
    public $tiposMovimiento = [];
    public $form = [];
    public $tipoFlujoSeleccionado = null;
    public function mount()
    {
        $this->fecha = now()->format('Y-m-d\TH:i');
        $this->form = [
            'tipo_movimiento_id' => null,
            'monto' => null,
            'metodo_pago' => null,
            'fecha' => now()->format('Y-m-d\TH:i'),
            'observacion' => null,
            'sucursal_id' => null
        ];
    }
    public function updatedTipoFlujoSeleccionado()
    {
        $this->form['tipo_movimiento_id'] = null;
        $this->obtenerTiposMovimiento();
    }
    public function obtenerTiposMovimiento()
    {
        if (!$this->tipoFlujoSeleccionado) {
            $this->tiposMovimiento = collect();
            return;
        }

        $this->tiposMovimiento = TipoMovimiento::query()
            ->where('activo', true)
            ->where('tipo_flujo', $this->tipoFlujoSeleccionado)
            ->where('es_automatico', false) // <--- CRÍTICO: No permite cargar Ventas/Compras manualmente
            ->where(function ($q) {
                $q->whereNull('cuenta_id')
                    ->orWhere('cuenta_id', auth()->user()->cuenta_id);
            })
            ->orderBy('nombre')
            ->get();
    }

    public function guardar()
    {
        $this->validate([
            'form.tipo_movimiento_id' => 'required|exists:tipos_movimiento,id',
            'form.monto' => 'required|numeric|min:0.01',
            'form.metodo_pago' => 'nullable|string|max:100',
            'form.fecha' => 'required|date',
            'form.observacion' => 'nullable|string|max:500',
            'form.sucursal_id' => 'required',
        ]);

        try {
            $data = [
                'tipo_movimiento_id' => $this->form['tipo_movimiento_id'],
                'monto' => (float) $this->form['monto'],
                'metodo_pago' => $this->form['metodo_pago'],
                'fecha' => $this->form['fecha'],
                'observacion' => $this->form['observacion'],
                'sucursal_id' => $this->form['sucursal_id'],

                // contexto del sistema (NO viene del formulario)
                'cuenta_id' => Auth::user()->cuenta->id,
                'usuario_id' => auth()->id(),
            ];

            app(MovimientoCajaServicio::class)->registrar($data);

            $this->alert('success', 'Movimiento registrado correctamente.');
            $this->resetExcept('fecha');
            $this->fecha = now()->format('Y-m-d\TH:i');
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.dueno_tienda.movimiento_panel.movimiento-form-component');
    }

}

