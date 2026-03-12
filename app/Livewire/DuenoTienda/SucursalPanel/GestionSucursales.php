<?php

namespace App\Livewire\DuenoTienda\SucursalPanel;

use App\Models\Sucursal;
use App\Services\Negocio\SucursalServicio;
use App\Traits\ConNegocioSeleccionado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class GestionSucursales extends Component
{
    use ConNegocioSeleccionado;
    public $sucursal;
    public $showForm = false;
    public $isEditing = false;

    // Campos del formulario
    public $nombre;
    public $direccion;
    public $telefono;
    public $email;
    public $es_principal = false;
    public $estado = true;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'es_principal' => 'boolean',
        'estado' => 'boolean',
    ];

    public function mount()
    {
        $this->cargarNegocioSeleccionado();
    }


    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
    }

    public function edit(string $uuid)
    {
        $sucursal = Sucursal::where('uuid', $uuid)->first();
        if (!$sucursal) {
            LivewireAlert::text('La sucursal ya no existe')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $this->resetForm();
        $this->sucursal = $sucursal;
        $this->nombre = $sucursal->nombre;
        $this->direccion = $sucursal->direccion;
        $this->telefono = $sucursal->telefono;
        $this->email = $sucursal->email;
        $this->es_principal = $sucursal->es_principal;
        $this->estado = $sucursal->estado;

        $this->showForm = true;
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        if($this->es_principal){
            Sucursal::where('negocio_id', $this->negocioSeleccionado->id)->update([
                'es_principal'=>false
            ]);
        }

        try {
            if ($this->isEditing) {
                $sucursal = $this->sucursal;
            } else {
                $sucursal = new Sucursal();
                $sucursal->uuid = Str::uuid();
            }

            $sucursal->negocio_id = $this->negocioSeleccionado->id;
            $sucursal->nombre = $this->nombre;
            $sucursal->direccion = $this->direccion;
            $sucursal->telefono = $this->telefono;
            $sucursal->email = $this->email;
            $sucursal->es_principal = $this->es_principal;
            $sucursal->estado = $this->estado;

            $sucursal->save();

            // Si es la primera sucursal del negocio, marcarla como principal
            $countSucursales = Sucursal::where('negocio_id', $this->negocioSeleccionado->id)->where('es_principal',true)->count();
            if ($countSucursales === 0) {
                $sucursal->es_principal = true;
                $sucursal->save();
            }

            LivewireAlert::text($this->isEditing ? 'Sucursal actualizada correctamente' : 'Sucursal creada correctamente')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

            $this->resetForm();

        } catch (\Exception $e) {
            LivewireAlert::text('Error al guardar la sucursal: ' . $e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function delete(string $uuid)
    {
        $sucursal = Sucursal::where('uuid', $uuid)->first();
        if (!$sucursal) {
            LivewireAlert::text('La sucursal ya no existe')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        try {

            $sucursal->delete();

            LivewireAlert::text('Sucursal eliminada correctamente')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

        } catch (\Exception $e) {
            LivewireAlert::text('Error al eliminar la sucursal: ' . $e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'sucursal',
            'nombre',
            'direccion',
            'telefono',
            'email',
            'es_principal',
            'estado',
            'showForm',
            'isEditing'
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        $negocio = Auth::user()->negocio_activo;
        $sucursales = SucursalServicio::porNegocio($negocio->id)->paginate();
    
        return view('livewire.dueno_tienda.sucursal_panel.gestion-sucursales', [
            'sucursales' => $sucursales,
        ]);
    }
}

