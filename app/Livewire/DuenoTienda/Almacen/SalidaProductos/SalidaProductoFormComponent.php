<?php

namespace App\Livewire\DuenoTienda\Almacen\SalidaProductos;
use App\Services\SalidaProductoServicio;
use App\Traits\ConNegocioSeleccionado;
use App\Traits\LivewireAlerta;
use InvalidArgumentException;
use Livewire\Component;


class SalidaProductoFormComponent extends Component
{
    use ConNegocioSeleccionado, LivewireAlerta;
    public $mostrarFormularioSalidaProducto = false;
    public $form = [
        'producto_id' => null,
        'sucursal_id' => null,
        'tipo_salida' => null,
        'cantidad' => null,
        'costo_unitario' => null,
        'fecha_salida' => null,
    ];

    public $productos = [];
    public $sucursales = [];
    protected $listeners = ['registrarSalidaProducto'];
    public function mount()
    {
        $this->cargarNegocioSeleccionado();
        $this->sucursales = $this->negocioSeleccionado->sucursales;
        $this->productos = $this->negocioSeleccionado->productos;

        // 👇 Si solo hay una sucursal, se asigna automáticamente
        if ($this->sucursales->count() > 0) {
            $this->form['sucursal_id'] = $this->sucursales->first()->id;
        }

        // Lo mismo para producto si quieres
        if ($this->productos->count() > 0) {
            $this->form['producto_id'] = $this->productos->first()->id;
        }

        $this->form['tipo_salida'] = 'POR AJUSTE DE INVENTARIO';
    }
    public function registrarSalidaProducto()
    {
        $this->mostrarFormularioSalidaProducto = true;
    }
    public function guardarSalidaProducto()
    {
        try {
            $data = [
                'producto_id' => $this->form['producto_id'],
                'sucursal_id' => $this->form['sucursal_id'],
                'tipo_salida' => $this->form['tipo_salida'],
                'cantidad' => $this->form['cantidad'],
                'costo_unitario' => $this->form['costo_unitario'],
                'fecha_salida' => $this->form['fecha_salida'],
                'referencia_id' => null,
                'referencia_tipo' => null,
            ];
            
            app(SalidaProductoServicio::class)->generarSalida($data);

            $this->alert('success', 'Registro de salida exitoso.');
            $this->mostrarFormularioSalidaProducto = false;
            $this->dispatch('refrescarSalidaProductos');
            // Limpia el formulario y cierra modal
            $this->reset('form');
            $this->alert('success', 'Registro de salida exitoso.');
        } catch (InvalidArgumentException $th) {
            $this->alert('error', $th->getMessage());
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.dueno_tienda.almacen.salida_productos.salida-producto-form');
    }
}