<?php

namespace App\Livewire\Superadmin\Unidades;

use App\Models\Unidad;
use App\Traits\LivewireAlerta;
use Livewire\Component;
use Livewire\WithPagination;

class GestionUnidades extends Component
{
    use WithPagination;
    use LivewireAlerta;

    public $isOpen = false;
    public $unidadId;
    public $uuid;
    public $nombre;
    public $abreviatura;
    public $tipo_negocio;
    public $activo = true;
    public $search = '';
    public $tipoNegocioFilter = '';

    public function render()
    {
        $unidades = Unidad::query()
            ->when($this->search, function ($query) {
                return $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('abreviatura', 'like', '%' . $this->search . '%');
            })
            ->when($this->tipoNegocioFilter, function ($query) {
                return $query->where('tipo_negocio', $this->tipoNegocioFilter);
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.superadmin.unidades.gestion-unidades', [
            'unidades' => $unidades,
        ]);
    }

    public function crear()
    {
        $this->resetValidation();
        $this->reset(['unidadId', 'uuid', 'nombre', 'abreviatura', 'tipo_negocio']);
        $this->activo = true;
        $this->isOpen = true;
    }

    public function editar($uuid)
    {
        $this->resetValidation();
        $unidad = Unidad::where('uuid', $uuid)->firstOrFail();
        $this->unidadId = $unidad->id;
        $this->uuid = $unidad->uuid;
        $this->nombre = $unidad->nombre;
        $this->abreviatura = $unidad->abreviatura;
        $this->tipo_negocio = $unidad->tipo_negocio;
        $this->activo = $unidad->activo;
        $this->isOpen = true;
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'abreviatura' => 'required|string|max:10',
            'tipo_negocio' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        if ($this->unidadId) {
            $unidad = Unidad::findOrFail($this->unidadId);
            $unidad->update([
                'nombre' => $this->nombre,
                'abreviatura' => $this->abreviatura,
                'tipo_negocio' => $this->tipo_negocio,
                'activo' => $this->activo,
            ]);
            $this->alert('success', 'Unidad actualizada correctamente.');
        } else {
            Unidad::create([
                'nombre' => $this->nombre,
                'abreviatura' => $this->abreviatura,
                'tipo_negocio' => $this->tipo_negocio,
                'activo' => $this->activo,
            ]);
            $this->alert('success', 'Unidad creada correctamente.');
        }

        $this->isOpen = false;
    }

    public function eliminar($uuid)
    {
        $unidad = Unidad::where('uuid', $uuid)->firstOrFail();
        
        // Verificar si tiene presentaciones
        if ($unidad->presentaciones()->count() > 0) {
            $this->alert('error', 'No se puede eliminar la unidad porque tiene presentaciones asociadas.');
            return;
        }
        
        $unidad->delete();
        $this->alert('success', 'Unidad eliminada correctamente.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }
}

