<?php

namespace App\Livewire\DuenoTienda\ClientePanel;
use App\Models\Cliente;
use App\Services\Comercial\ClienteServicio;
use App\Services\Facturacion\Sunat\SunatServicio;
use App\Traits\LivewireAlerta;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

class GestionClientes extends Component
{
    use LivewireAlerta;
    // Variables de filtro
    public $numero_documentoFiltro;
    public $telefonoFiltro;
    public $tipo_cliente_idFiltro;
    public $nombre_comercialFiltro;

    // Nuevos filtros recomendados
    public $nombre_completoFiltro;
    public $emailFiltro;
    public $whatsappFiltro;
    public $tipo_cliente_id;
    public $tipo_documento_id;
    public $numero_documento;
    public $nombre_completo;
    public $nombre_comercial;
    public $email;
    public $telefono;
    public $whatsapp;
    public $direccion;
    public $distrito;
    public $provincia;
    public $departamento;
    public $puntos = 0;
    public $notas;
    public $clienteId; // para manejar edición
    public $mostrarFormulario = false; // si usas toggle para mostrar/ocultar formulario
    public function crearCliente()
    {
        $this->mostrarFormulario = true;
    }
    public function guardarCliente()
    {
        try {
            $this->validate([
                'tipo_cliente_id' => 'required|in:persona,empresa',
                'tipo_documento_id' => 'required|string|max:2',
                'numero_documento' => 'nullable|string|max:20',
                'nombre_completo' => 'required',
                'nombre_comercial' => 'nullable',
                'email' => 'nullable|email',
                'telefono' => 'nullable|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'direccion' => 'nullable|string',
                'distrito' => 'nullable|string',
                'provincia' => 'nullable|string',
                'departamento' => 'nullable|string',
                'puntos' => 'nullable|integer|min:0',
                'notas' => 'nullable|string',
            ]);

            $data = $this->only([
                'tipo_cliente_id',
                'tipo_documento_id',
                'numero_documento',
                'nombre_completo',
                'nombre_comercial',
                'email',
                'telefono',
                'whatsapp',
                'direccion',
                'distrito',
                'provincia',
                'departamento',
                'puntos',
                'notas'
            ]);

            $data['cliente_id'] = $this->clienteId; // Para editar si aplica

            ClienteServicio::guardar($data);

            $this->resetFormulario(); // (implementa este método si necesitas limpiar)
            $this->alert('success', 'Cliente guardado correctamente');
            $this->mostrarFormulario = false;
        } catch (\Throwable $th) {
            report($th);
            $this->alert('error', 'Error al guardar el cliente, revisar reporte interno');
        }
    }
    private function resetFormulario()
    {
        $this->reset([
            'tipo_cliente_id',
            'tipo_documento_id',
            'numero_documento',
            'nombre_completo',
            'nombre_comercial',
            'email',
            'telefono',
            'whatsapp',
            'direccion',
            'distrito',
            'provincia',
            'departamento',
            'puntos',
            'notas',
            'clienteId'
        ]);
    }
    public function editarCliente($id)
    {
        $this->resetFormulario();
        $cliente = Cliente::findOrFail($id);

        $this->clienteId = $cliente->id;
        $this->fill([
            'tipo_cliente_id' => $cliente->tipo_cliente_id,
            'tipo_documento_id' => $cliente->tipo_documento_id,
            'numero_documento' => $cliente->numero_documento,
            'nombre_completo' => $cliente->nombre_completo,
            'nombre_comercial' => $cliente->nombre_comercial,
            'email' => $cliente->email,
            'telefono' => $cliente->telefono,
            'whatsapp' => $cliente->whatsapp,
            'direccion' => $cliente->direccion,
            'distrito' => $cliente->distrito,
            'provincia' => $cliente->provincia,
            'departamento' => $cliente->departamento,
            'puntos' => $cliente->puntos,
            'notas' => $cliente->notas
        ]);

        $this->mostrarFormulario = true;
    }
    public function eliminarCliente($id)
    {
        try {
            ClienteServicio::eliminar($id);
            $this->alert('success', 'Cliente eliminado correctamente.');
        } catch (\Throwable $th) {
            report($th);
            $this->alert('error', 'No se pudo eliminar el cliente. Verifica permisos o errores internos.');
        }
    }
    public function sunat()
    {
        try {
            $data = SunatServicio::consultarPorRuc($this->numero_documento);

            $this->numero_documento = $data['ruc']; // ya lo tenías
            $this->nombre_completo = $data['razonSocial']; // si es persona
            $this->nombre_comercial = $data['nombreComercial']; // si es empresa

            $this->direccion = $data['direccion'];
            $this->departamento = $data['departamento'];
            $this->provincia = $data['provincia'];
            $this->distrito = $data['distrito'];

            $this->telefono = implode(', ', $data['telefonos'] ?? []);

            $this->alert('success', 'Datos obtenidos con éxito.');

        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
    public function render()
    {
        $clientes = collect();

        try {

            $filtros = [
                'numero_documento' => $this->numero_documentoFiltro ?? null,
                'telefono' => $this->telefonoFiltro ?? null,
                'tipo_cliente_id' => $this->tipo_cliente_idFiltro ?? null,
                'nombre_comercial' => $this->nombre_comercialFiltro ?? null,
            ];

            $query = ClienteServicio::listarClientes($filtros);
            $clientes = $query->paginate(20);

        } catch (\Throwable $th) {
            $this->alert('error', 'Error al listar los clientes, revisar reporte interno');
            report($th);
            $clientes = new LengthAwarePaginator([], 0, 20, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        return view('livewire.dueno_tienda.cliente_panel.gestion-clientes', [
            'clientes' => $clientes,
        ]);
    }

}