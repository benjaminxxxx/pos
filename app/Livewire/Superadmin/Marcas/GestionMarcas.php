<?php

namespace App\Livewire\Superadmin\Marcas;

use App\Models\CategoriaProducto;
use App\Models\Marca;
use App\Traits\LivewireAlerta;
use Livewire\Component;
use Livewire\WithPagination;

class GestionMarcas extends Component
{
    use WithPagination;
    use LivewireAlerta;

    public $isOpen = false;
    public $marcaId;
    public $uuid;
    public $descripcion_marca;
    public $categoria_id;
    public $tipo_negocio;
    public $activo = true;
    public $search = '';
    public $tipoNegocioFilter = '';


    public function render()
    {
        $marcas = Marca::query()
            ->when($this->search, function ($query) {
                return $query->where('descripcion_marca', 'like', '%' . $this->search . '%');
            })
            ->when($this->tipoNegocioFilter, function ($query) {
                return $query->where('tipo_negocio', $this->tipoNegocioFilter);
            })
            ->orderBy('descripcion_marca')
            ->paginate(10);

        return view('livewire.superadmin.marcas.gestion-marcas', [
            'marcas' => $marcas,
            'categorias' => CategoriaProducto::all(),
        ]);
    }

    public function crear()
    {
        $this->resetValidation();
        $this->reset(['marcaId', 'uuid', 'descripcion_marca', 'categoria_id', 'tipo_negocio']);
        $this->activo = true;
        $this->isOpen = true;
    }

    public function editar($uuid)
    {
        $this->resetValidation();
        $marca = Marca::where('uuid', $uuid)->firstOrFail();
        $this->marcaId = $marca->id;
        $this->uuid = $marca->uuid;
        $this->descripcion_marca = $marca->descripcion_marca;
        $this->categoria_id = $marca->categoria_id;
        $this->tipo_negocio = $marca->tipo_negocio;
        $this->activo = $marca->activo;
        $this->isOpen = true;
    }

    public function guardar()
    {
        $this->validate([
            'descripcion_marca' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:categorias_productos,id',
            'tipo_negocio' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        if ($this->marcaId) {
            $marca = Marca::findOrFail($this->marcaId);
            $marca->update([
                'descripcion_marca' => $this->descripcion_marca,
                'categoria_id' => $this->categoria_id,
                'tipo_negocio' => $this->tipo_negocio,
                'activo' => $this->activo,
            ]);
            $this->alert('success', 'Marca actualizada correctamente.');
        } else {
            Marca::create([
                'descripcion_marca' => $this->descripcion_marca,
                'categoria_id' => $this->categoria_id,
                'tipo_negocio' => $this->tipo_negocio,
                'activo' => $this->activo,
            ]);
            $this->alert('success', 'Marca creada correctamente.');
        }

        $this->isOpen = false;
    }

    public function eliminar($uuid)
    {
        $marca = Marca::where('uuid', $uuid)->firstOrFail();
        
        // Verificar si tiene productos
        if ($marca->productos()->count() > 0) {
            // Establecer marca_id a null en los productos
            $marca->productos()->update(['marca_id' => null]);
        }
        
        $marca->delete();
        $this->alert('success', 'Marca eliminada correctamente.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }
}

