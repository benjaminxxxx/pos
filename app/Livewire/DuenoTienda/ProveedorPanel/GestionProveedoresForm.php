<?php

namespace App\Livewire\DuenoTienda\ProveedorPanel;

use App\Models\TipoDocumentoSunat;
use App\Services\ProveedorServicio;
use App\Traits\LivewireAlerta;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GestionProveedoresForm extends Component
{
    use LivewireAlerta;

    public $mostrarFormularioProveedores = false;
    public $proveedorId;

    // Propiedades del formulario
    public $documento_tipo, $documento_numero, $razon_social, $nombre_comercial;
    public $direccion, $telefono, $email, $pagina_web;
    public $banco, $cuenta_bancaria, $cci;
    public $estado = 'ACTIVO';

    public $tipoDocumentos = [];

    protected $listeners = ['registrarProveedor', 'editarProveedor'];
    private ProveedorServicio $proveedorServicio;

    // Inyección de dependencias para el servicio
    public function boot(ProveedorServicio $proveedorServicio)
    {
        $this->proveedorServicio = $proveedorServicio;
    }

    public function mount()
    {
        $this->tipoDocumentos = TipoDocumentoSunat::all();
    }

    private function resetFormulario()
    {
        $this->resetErrorBag();
        $this->resetExcept('mostrarFormularioProveedores', 'tipoDocumentos');
        $this->estado = 1; // Valor por defecto
    }

    public function registrarProveedor()
    {
        $this->resetFormulario();
        $this->mostrarFormularioProveedores = true;
    }

    public function editarProveedor(string $uuid)
    {
        $this->resetFormulario();
        try {
            $cuentaId = auth()->user()->cuenta->id;
            $proveedor = $this->proveedorServicio->obtenerPorUuid($uuid, $cuentaId);

            if ($proveedor) {
                $this->fill($proveedor->toArray()); // Rellena las propiedades automáticamente
                $this->proveedorId = $proveedor->id;
                $this->mostrarFormularioProveedores = true;
            } else {
                $this->alert('error', 'No se encontró el proveedor.');
            }
        } catch (\Throwable $th) {
            Log::error('Error al cargar proveedor para editar: ' . $th->getMessage());
            $this->alert('error', 'Ocurrió un error al cargar los datos del proveedor.');
        }
    }

    public function storeProveedor()
    {
        $cuentaId = auth()->user()->cuenta->id;

        $datosValidados = $this->validate([
            'documento_tipo' => 'required|string|max:10',
            'documento_numero' => [
                'required', 'string', 'max:20',
                Rule::unique('proveedores')->where('cuenta_id', $cuentaId)->ignore($this->proveedorId)
            ],
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pagina_web' => 'nullable|url|max:255',
            'banco' => 'nullable|string|max:100',
            'cuenta_bancaria' => 'nullable|string|max:100',
            'cci' => 'nullable|string|max:100',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ],[
            'estado.in' => 'El estado debe ser ACTIVO o INACTIVO.',
        ]);

        $datosProveedor = array_merge($datosValidados, ['cuenta_id' => $cuentaId]);

        try {
            $this->proveedorServicio->crearOActualizar($datosProveedor, $this->proveedorId);
            $mensaje = $this->proveedorId ? 'Proveedor actualizado con éxito' : 'Proveedor registrado con éxito';
            $this->alert('success', $mensaje);

            $this->mostrarFormularioProveedores = false;
            $this->dispatch('proveedorGuardado');
        } catch (\Exception $e) {
            Log::error('Error al guardar proveedor: ' . $e->getMessage());
            $this->alert('error', 'Ocurrió un error inesperado al guardar el proveedor.');
        }
    }

    public function render()
    {
        return view('livewire.dueno_tienda.proveedores_panel.gestion-proveedores-form');
    }
}