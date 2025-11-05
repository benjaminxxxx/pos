<?php

namespace App\Livewire\DuenoTienda\Almacen\EntradaProductos;
use App\Models\ProductoEntrada;
use App\Services\EntradaProductoServicio;
use App\Traits\ConNegocioSeleccionado;
use App\Traits\LivewireAlerta;
use InvalidArgumentException;
use Livewire\Component;
use Session;


class EntradaProductoFormComponent extends Component
{
    use ConNegocioSeleccionado, LivewireAlerta;
    public $mostrarFormularioEntradaProducto = false;
    public $form = [
        'producto_id' => null,
        'sucursal_id' => null,
        'tipo_entrada' => null,
        'cantidad' => null,
        'costo_unitario' => null,
        'fecha_ingreso' => null,
    ];

    public $productos = [];
    public $sucursales = [];
    protected $listeners = ['registrarEntradaProducto'];
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

        $this->form['tipo_entrada'] = 'POR INVENTARIO INICIAL';
    }
    public function registrarEntradaProducto()
    {
        $this->mostrarFormularioEntradaProducto = true;
    }
    public function guardarEntradaProducto()
    {
        try {
            $data = [
                'producto_id' => $this->form['producto_id'],
                'sucursal_id' => $this->form['sucursal_id'],
                'tipo_entrada' => $this->form['tipo_entrada'],
                'cantidad' => $this->form['cantidad'],
                'costo_unitario' => $this->form['costo_unitario'],
                'fecha_ingreso' => $this->form['fecha_ingreso'],
            ];

            app(EntradaProductoServicio::class)->crear($data);

            $this->alert('success', 'Registro de entrada exitoso.');
            $this->mostrarFormularioEntradaProducto = false;
            $this->dispatch('refrescarEntradaProductos');
            // Limpia el formulario y cierra modal
            $this->reset('form');
            $this->alert('success', 'Registro de entrada exitoso.');
        } catch (InvalidArgumentException $th) {
            $this->alert('error', $th->getMessage());
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.dueno_tienda.almacen.entrada_productos.entrada-producto-form');
    }
}