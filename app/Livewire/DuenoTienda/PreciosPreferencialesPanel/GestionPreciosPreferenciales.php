<?php

namespace App\Livewire\DuenoTienda\PreciosPreferencialesPanel;

use App\Services\ProductoServicio;
use App\Traits\LivewireAlerta;
use App\Traits\SeleccionaNegocio;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;


class GestionPreciosPreferenciales extends Component
{
    use WithPagination, WithFileUploads, SeleccionaNegocio, LivewireAlerta;

    // ------------------------------
    // Filtros y búsqueda
    // ------------------------------
    public $search = '';
    public $categoriaFilter = '';
    public $marcaFilter = '';
    public $activoFilter = '';

    // ------------------------------
    // Control del modal y selección
    // ------------------------------
    public $isOpen = false;
    public $producto_id;
    public $uuid;

   

    public function mount()
    {
        // Inicializar la selección de negocio
        $this->mountSeleccionaNegocio();
        // Si hay un negocio seleccionado, cargar sus sucursales
        if ($this->negocioSeleccionado) {
            
        }
    }

    public function resetearComponente()
    {
        // Este método se llama cuando se cambia de negocio
        $this->reset(['search', 'categoriaFilter', 'marcaFilter', 'activoFilter']);
        $this->resetPage();

        if ($this->negocioSeleccionado) {
            
        }
    }

    public function cargarSucursales()
    {
        if ($this->negocio_id) {
           
        }
    }

    public function render()
    {
        if (!$this->negocioSeleccionado) {
            return view('livewire.dueno_tienda.precios_preferenciales.gestion-precios-preferenciales', [
                'productos' => collect(),
                'categorias' => collect(),
                'marcas' => collect()
            ]);
        }

        $productos = ProductoServicio::buscar([
            'negocio_id' => $this->negocioSeleccionado->id,
            'search' => $this->search,
            'categoria_id' => $this->categoriaFilter,
            'marca_id' => $this->marcaFilter,
            'activo' => $this->activoFilter,
        ]);

        return view('livewire.dueno_tienda.precios_preferenciales.gestion-precios-preferenciales', [
            'productos' => $productos,
        ]);
    }

    public function resetFormulario()
    {
        $this->resetValidation();

        $this->reset([
            'producto_id',
            'uuid',
            'codigo_barra',
        ]);
        $this->negocio_id = $this->negocioSeleccionado ? $this->negocioSeleccionado->id : null;
    }

    public function create()
    {
        $this->resetFormulario();
        $this->isOpen = true;
    }

    public function edit($uuid)
    {
        $this->resetFormulario();
        try {
            $producto = ProductoServicio::obtenerProductoPorUuid($uuid);
            
            $this->isOpen = true;
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function store()
    {
        $this->validate([
            'codigo_barra' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('productos', 'codigo_barra')->ignore($this->producto_id),
            ],
        ]);

        try {

            $data = [
                'producto_id' => $this->producto_id,
                'codigo_barra' => $this->codigo_barra,
            ];
            
            ProductoServicio::guardar($data);

            $this->alert('success', $this->producto_id ? 'Producto actualizado correctamente.' : 'Producto creado correctamente.');
            $this->closeModal();
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function delete($uuid)
    {
        try {
            ProductoServicio::eliminarProductoPorUuid($uuid);
            $this->alert('success', 'Producto eliminado correctamente.');
        } catch (\Illuminate\Validation\UnauthorizedException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->alert('error', 'Producto no encontrado.');
        } catch (\Exception $e) {
            $this->alert('error', 'Error inesperado al eliminar el producto.');
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }
}

