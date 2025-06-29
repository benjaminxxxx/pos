<?php

namespace App\Livewire\Superadmin\Unidades;

use App\Models\Unidad;
use App\Models\UnidadComercial;
use App\Services\Producto\UnidadServicio;
use App\Traits\LivewireAlerta;
use Livewire\Component;
use Livewire\WithPagination;

class GestionUnidades extends Component
{
    use WithPagination, LivewireAlerta;

    public $isOpen = false;
    public $estaEditando = false;

    public $codigo;
    public $descripcion;
    public $alt;
    public $search = '';
    public $mostrarFormularioEspecial = false;
    public $nombre_comercial;

    public function render()
    {
        $unidades = Unidad::query()
            ->when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where('codigo', 'like', $searchTerm)
                    ->orWhere('descripcion', 'like', $searchTerm);
            })
            ->orderBy('descripcion')
            ->paginate(10);

        return view('livewire.superadmin.unidades.gestion-unidades', [
            'unidades' => $unidades,
        ]);
    }

    public function crear()
    {
        $this->resetValidation();
        $this->reset(['nombre_comercial']);
        $this->mostrarFormularioEspecial = true;
    }

    public function editar($codigo)
    {
        $this->resetValidation();

        $unidad = Unidad::where('codigo', $codigo)->firstOrFail();

        $this->codigo = $unidad->codigo;
        $this->descripcion = $unidad->descripcion;
        $this->alt = $unidad->alt;
        $this->estaEditando = true;
        $this->isOpen = true;
    }
    public function guardarUnidadEspecial()
    {
        try {
            UnidadServicio::crearUnidadComercial($this->nombre_comercial);
            $this->mostrarFormularioEspecial = false;
            $this->alert('success', 'Registro exitoso.');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }
    public function guardar()
    {
        $this->validate([
            'codigo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'alt' => 'nullable|string|max:255',
        ]);

        if ($this->estaEditando) {
            // Solo se actualiza descripción y alt, no el código
            $unidad = Unidad::where('codigo', $this->codigo)->firstOrFail();
            $unidad->update([
                'descripcion' => $this->descripcion,
                'alt' => $this->alt,
            ]);
            $this->alert('success', 'Unidad actualizada correctamente.');
        } else {
            Unidad::create([
                'codigo' => $this->codigo,
                'descripcion' => $this->descripcion,
                'alt' => $this->alt,
            ]);
            $this->alert('success', 'Unidad creada correctamente.');
        }

        $this->isOpen = false;
    }

    public function eliminar($codigo)
    {
        $this->alert('error', 'Por el momento no se puede eliminar.');
        /*
        $unidad = Unidad::where('codigo', $codigo)->firstOrFail();

        if ($unidad->presentaciones()->count() > 0) {
            $this->alert('error', 'No se puede eliminar la unidad porque tiene presentaciones asociadas.');
            return;
        }

        $unidad->delete();
        $this->alert('success', 'Unidad eliminada correctamente.');*/
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }
}
