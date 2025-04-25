<?php

namespace App\Livewire\Superadmin\Categorias;

use App\Models\CategoriaProducto;
use App\Traits\LivewireAlerta;
use Livewire\Component;
use Livewire\WithPagination;

class GestionCategorias extends Component
{
    use WithPagination;
    use LivewireAlerta;

    public $isOpen = false;
    public $categoriaId;
    public $uuid;
    public $descripcion;
    public $categoria_id;
    public $tipo_negocio;
    public $activo = true;
    public $search = '';
    public $tipoNegocioFilter = '';

    public function render()
    {

        $categorias = CategoriaProducto::query()
            ->when($this->search, function ($query) {
                return $query->where('descripcion', 'like', '%' . $this->search . '%');
            })
            ->when($this->tipoNegocioFilter, function ($query) {
                return $query->where('tipo_negocio', $this->tipoNegocioFilter);
            })
            ->orderBy('descripcion')
            ->paginate(10);

        return view('livewire.superadmin.categorias.gestion-categorias', [
            'categorias' => $categorias,
            'categoriasPadre' => CategoriaProducto::where('categoria_id', null)->get(),
        ]);
    }

    public function crear()
    {
        $this->resetValidation();
        $this->reset(['categoriaId', 'uuid', 'descripcion', 'categoria_id', 'tipo_negocio']);
        $this->activo = true;
        $this->isOpen = true;
    }

    public function editar($uuid)
    {
        $this->resetValidation();
        $categoria = CategoriaProducto::where('uuid', $uuid)->firstOrFail();
        $this->categoriaId = $categoria->id;
        $this->uuid = $categoria->uuid;
        $this->descripcion = $categoria->descripcion;
        $this->categoria_id = $categoria->categoria_id;
        $this->tipo_negocio = $categoria->tipo_negocio;
        $this->activo = $categoria->activo;
        $this->isOpen = true;
    }

    public function guardar()
    {
        $this->validate([
            'descripcion' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:categorias_productos,id',
            'tipo_negocio' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $this->categoria_id = $this->categoria_id==''?null:$this->categoria_id;

        if ($this->categoriaId) {
            $categoria = CategoriaProducto::findOrFail($this->categoriaId);
            $categoria->update([
                'descripcion' => $this->descripcion,
                'categoria_id' => $this->categoria_id,
                'tipo_negocio' => $this->tipo_negocio,
                'activo' => $this->activo,
            ]);

            $this->alert('success', 'Categoría actualizada correctamente.');
        } else {
            CategoriaProducto::create([
                'descripcion' => $this->descripcion,
                'categoria_id' => $this->categoria_id,
                'tipo_negocio' => $this->tipo_negocio,
                'activo' => $this->activo,
            ]);
            $this->alert('success', 'creada actualizada correctamente.');
        }

        $this->isOpen = false;
    }

    public function eliminar($uuid)
    {
        $categoria = CategoriaProducto::where('uuid', $uuid)->firstOrFail();

        // Verificar si tiene subcategorías
        if ($categoria->subcategorias()->count() > 0) {

            $this->alert('error', 'No se puede eliminar la categoría porque tiene subcategorías asociadas.');
            return;
        }

        // Verificar si tiene productos
        if ($categoria->productos()->count() > 0) {
            // Establecer categoria_id a null en los productos
            $categoria->productos()->update(['categoria_id' => null]);
        }

        $categoria->delete();
        $this->alert('success', 'Categoría eliminada correctamente.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }
}

