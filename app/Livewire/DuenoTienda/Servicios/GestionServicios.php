<?php

namespace App\Livewire\DuenoTienda\Servicios;

use App\Models\CategoriaProducto;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Traits\SeleccionaNegocio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class GestionServicios extends Component
{
    use WithPagination;
    use WithFileUploads;
    use SeleccionaNegocio;

    // Propiedades para la lista
    public $search = '';
    public $categoriaFilter = '';
    public $sucursalFilter = '';
    public $activoFilter = '';

    // Propiedades para el formulario
    public $servicio_id;
    public $uuid;
    public $codigo;
    public $nombre_servicio;
    public $descripcion;
    public $precio;
    public $igv;
    public $categoria_id;
    public $sucursal_id;
    public $activo = true;

    // Propiedades para selects
    public $sucursales = [];
    public $categorias = [];
    public $isOpen = false;
    protected $listeners = ['negocio-seleccionado'=> 'regenerarValores'];

    public function mount()
    {
        $this->mountSeleccionaNegocio();
        $this->cargarDatosNegocio();
    }
    public function regenerarValores(){
        $this->resetValidation();
        $this->limpiarCampos();
        $this->cargarDatosNegocio();
    }
    public function cargarDatosNegocio()
    {
        if ($this->negocioSeleccionado) {
            $this->sucursales = Sucursal::where('negocio_id', $this->negocioSeleccionado->id)->get();
            if ($this->sucursales->count() > 0) {
                $this->sucursal_id = $this->sucursales->first()->id;
            }
            $this->categorias = $this->negocioSeleccionado->categorias();
        }
    }

    public function updatedNegocioSeleccionado()
    {
        $this->cargarDatosNegocio();
    }

    public function render()
    {
        $query = Servicio::query();

        if ($this->negocioSeleccionado) {
            $query->where('negocio_id', $this->negocioSeleccionado->id);
        }

        if ($this->search) {
            $this->resetPage();
            $query->where(function ($q) {
                $q->where('nombre_servicio', 'like', '%' . $this->search . '%')
                    ->orWhere('codigo', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoriaFilter) {
            $query->where('categoria_id', $this->categoriaFilter);
        }

        if ($this->sucursalFilter) {
            $query->where('sucursal_id', $this->sucursalFilter);
        }

        if ($this->activoFilter !== '') {
            $query->where('activo', $this->activoFilter);
        }

        $servicios = $query->orderBy('nombre_servicio')->paginate(10);

        return view('livewire.dueno_tienda.servicios.gestion-servicios', [
            'servicios' => $servicios
        ]);
    }

    public function create()
    {
        $this->resetValidation();
        $this->limpiarCampos();
        $this->isOpen = true;
    }

    public function editar($uuid)
    {
        if (!$this->negocioSeleccionado) {
            LivewireAlert::text('Debe seleccionar un negocio primero.')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $servicio = Servicio::where('uuid', $uuid)->firstOrFail();

        // Verificar permisos
        if (Auth::user()->hasRole('vendedor') && $servicio->creado_por != Auth::id()) {
         
            LivewireAlert::text('No tienes permisos para editar este servicio.')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $this->servicio_id = $servicio->id;
        $this->uuid = $servicio->uuid;
        $this->codigo = $servicio->codigo;
        $this->nombre_servicio = $servicio->nombre_servicio;
        $this->descripcion = $servicio->descripcion;
        $this->precio = $servicio->precio;
        $this->igv = $servicio->igv;
        $this->categoria_id = $servicio->categoria_id;
        $this->sucursal_id = $servicio->sucursal_id;
        $this->activo = $servicio->activo;

        $this->isOpen = true;
    }

    public function guardar()
    {
        if (!$this->negocioSeleccionado) {

            LivewireAlert::text('Debe seleccionar un negocio primero.')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $this->validate([
            'codigo' => 'nullable|string|max:255',
            'nombre_servicio' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'igv' => 'nullable|numeric|min:0|max:100',
            'categoria_id' => 'nullable|exists:categorias_productos,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'activo' => 'boolean'
        ]);


        $mensajeExitoso = "Servicio creado correctamente.";

        try {
            if ($this->servicio_id) {
                $servicio = Servicio::findOrFail($this->servicio_id);
                $mensajeExitoso = "Servicio actualizado correctamente.";
            } else {
                $servicio = new Servicio();
                $servicio->negocio_id = $this->negocioSeleccionado->id;
                $servicio->creado_por = Auth::id();
            }

            $servicio->codigo = $this->codigo;
            $servicio->nombre_servicio = $this->nombre_servicio;
            $servicio->descripcion = $this->descripcion;
            $servicio->precio = $this->precio;
            $servicio->igv = $this->igv;
            $servicio->categoria_id = $this->categoria_id;
            $servicio->sucursal_id = $this->sucursal_id;
            $servicio->activo = $this->activo;

            $servicio->save();

            LivewireAlert::text($mensajeExitoso)
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

            $this->limpiarCampos();
            $this->closeModal();
        } catch (\Exception $e) {

            LivewireAlert::text('Error al guardar el servicio: ' . $e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }

    }

    public function eliminar($uuid)
    {
        $servicio = Servicio::where('uuid', $uuid)->firstOrFail();

        // Verificar permisos
        if (Auth::user()->hasRole('vendedor') && $servicio->creado_por != Auth::id()) {
          
            LivewireAlert::text('No tienes permisos para eliminar este servicio.')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        try {

            $servicio->delete();
            LivewireAlert::text('Servicio eliminado correctamente.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
          
            LivewireAlert::text('Error al eliminar el servicio: ' . $e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function limpiarCampos()
    {
        $this->reset([
            'servicio_id',
            'uuid',
            'codigo',
            'nombre_servicio',
            'descripcion',
            'precio',
            'igv',
            'categoria_id'
        ]);
        $this->activo = true;
    }

    public function cancelar()
    {
        $this->limpiarCampos();
        $this->closeModal();
    }
    public function closeModal()
    {
        $this->isOpen = false;
    }
}

