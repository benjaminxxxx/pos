<?php

namespace App\Livewire\DuenoTienda\NegocioPanel;

use App\Models\Negocio;
use App\Models\InformacionAdicional;
use App\Services\Negocio\NegocioServicio;
use App\Traits\LivewireAlerta;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Session;

class GestionNegocios extends Component
{
    use WithFileUploads;
    use LivewireAlerta;

    public $negocios;
    public $negocio;
    public $mostrarFormularioNegocio = false;
    public $isEditing = false;
    public $activeTab = 'general';

    // Campos del formulario - Pestaña General
    public $nombre_legal;
    public $nombre_comercial;
    public $ruc;
    public $direccion;
    public $ubigeo;
    public $departamento;
    public $provincia;
    public $distrito;
    public $codigo_pais;
    public $urbanizacion;
    public $tipo_negocio;
    public $usuario_sol;
    public $clave_sol;
    public $client_secret;
    public $modo = 'desarrollo'; // Valor por defecto
    public $certificado;
    public $certificado_a_eliminar;
    public $certificado_eliminada = false;
    public $certificado_actual;

    // Para mostrar nombres de archivos actuales
    public $logo_actual;
    public $logo_factura;
    public $logo_factura_a_eliminar;
    public $logo_factura_eliminada = false;

    // Campos temporales para agregar nueva información
    public $nuevaClave = '';
    public $nuevoValor = '';
    public $nuevaUbicacion = 'Cabecera';
    public $tiposNegocio = [];
    public $infoAdicional = [
        'Cabecera' => [],
        'Centro' => [],
        'Pie' => [],
    ];


    protected $rules = [
        'nombre_legal' => 'required|string|max:255',
        'nombre_comercial' => 'required|string|max:255',
        'ruc' => 'required|string|size:11',
        'direccion' => 'required|string|max:255',
        'tipo_negocio' => 'required|string|max:50',
        'usuario_sol' => 'required|string|max:50',
        'clave_sol' => 'required|string|max:50',
        'client_secret' => 'nullable|string',
        'modo' => 'required|in:desarrollo,produccion',
        'certificado' => 'nullable|file|max:2048', // sin validar mimes
        'logo_factura' => 'nullable|file|mimes:jpg,jpeg,png',
        'codigo_pais' => 'required'
    ];

    public function mount()
    {
        $this->loadNegocios();
        $this->tiposNegocio = config('negocios.tipos');
    }

    public function loadNegocios()
    {
        $this->negocios = Auth::user()->negocios;
    }



    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function create()
    {
        $this->resetForm();
        $this->mostrarFormularioNegocio = true;
        $this->isEditing = false;
        $this->activeTab = 'general';
    }

    public function edit(string $uuid)
    {
        $negocio = Negocio::where('uuid', $uuid)->first();
        if (!$negocio) {
            $this->alert('error', 'El negocio ya no existe');
            return;
        }
        $this->resetForm();
        $this->negocio = $negocio;
        $this->nombre_legal = $negocio->nombre_legal;
        $this->nombre_comercial = $negocio->nombre_comercial;
        $this->ruc = $negocio->ruc;
        $this->direccion = $negocio->direccion;
        $this->ubigeo = $negocio->ubigeo;
        $this->departamento = $negocio->departamento;
        $this->provincia = $negocio->provincia;
        $this->distrito = $negocio->distrito;
        $this->codigo_pais = $negocio->codigo_pais;
        $this->urbanizacion = $negocio->urbanizacion;
        $this->tipo_negocio = $negocio->tipo_negocio;
        $this->usuario_sol = $negocio->usuario_sol;
        $this->clave_sol = $negocio->clave_sol;
        $this->client_secret = $negocio->client_secret;
        $this->modo = $negocio->modo;

        // Guardamos referencia a los nombres de archivos actuales
        $this->logo_actual = $negocio->logo_factura;
        $this->logo_factura_a_eliminar = null;
        $this->logo_factura_eliminada = false;
        $this->logo_factura = null;

        $this->certificado = null;
        $this->certificado_a_eliminar = null;
        $this->certificado_actual = $negocio->certificado;
        $this->certificado_eliminada = false;

        // Cargar información adicional
        $this->cargarInformacionAdicional($negocio->id);

        $this->mostrarFormularioNegocio = true;
        $this->isEditing = true;
        $this->activeTab = 'general';
    }
    public function eliminarImagen()
    {
        $this->logo_factura = null;
        $this->logo_factura_a_eliminar = $this->logo_actual;
        $this->logo_actual = null;
        $this->logo_factura_eliminada = true;
    }
    public function eliminarImagenCertificado()
    {
        $this->certificado = null;
        $this->certificado_a_eliminar = $this->certificado;
        $this->certificado_actual = null;
        $this->certificado_eliminada = true;
    }
    public function cargarInformacionAdicional($negocioId)
    {
        $this->infoAdicional = [
            'Cabecera' => [],
            'Centro' => [],
            'Pie' => [],
        ];

        $informacionAdicional = InformacionAdicional::where('negocio_id', $negocioId)->get();

        foreach ($informacionAdicional as $info) {

            $this->infoAdicional[$info->ubicacion][] = [
                'id' => $info->id,
                'clave' => $info->clave,
                'valor' => $info->valor
            ];
        }
    }

    public function guardarNegocio()
    {
        $this->validate();
        $info = [
            'info_cabecera' => $this->infoAdicional['Cabecera'] ?? [],
            'info_centro' => $this->infoAdicional['Centro'] ?? [],
            'info_pie' => $this->infoAdicional['Pie'] ?? [],
        ];
        try {
            $data = [
                'nombre_legal' => $this->nombre_legal,
                'nombre_comercial' => $this->nombre_comercial,
                'ruc' => $this->ruc,
                'direccion' => $this->direccion,
                'ubigeo' => $this->ubigeo,
                'departamento' => $this->departamento,
                'provincia' => $this->provincia,
                'distrito' => $this->distrito,
                'codigo_pais' => $this->codigo_pais,
                'urbanizacion' => $this->urbanizacion,
                'tipo_negocio' => $this->tipo_negocio,
                'usuario_sol' => $this->usuario_sol,
                'clave_sol' => $this->clave_sol,
                'client_secret' => $this->client_secret,
                'modo' => $this->modo,
                'logo_factura' => $this->logo_factura,
                'logo_factura_a_eliminar' => $this->logo_factura_a_eliminar,
                'logo_factura_eliminada' => $this->logo_factura_eliminada,
                'certificado' => $this->certificado,
                'certificado_a_eliminar' => $this->certificado_a_eliminar,
                'certificado_eliminada' => $this->certificado_eliminada,
                'info_cabecera' => $info['info_cabecera'],
                'info_centro' => $info['info_centro'],
                'info_pie' => $info['info_pie'],
            ];

            NegocioServicio::guardar($data, $this->isEditing ? $this->negocio : null);

            $this->alert('success', $this->isEditing ? 'Negocio actualizado.' : 'Negocio creado.');
            $this->resetForm();
            $this->loadNegocios();

        } catch (\Throwable $e) {
            $this->alert('error', 'Error: ' . $e->getMessage());
        }
    }

    public function eliminarNegocio(string $uuid)
    {
        $negocio = Negocio::where('uuid', $uuid)->first();
        if (!$negocio) {
            $this->alert('error', 'El negocio ya no existe');
            return;
        }
        $negocio->delete();
        $this->loadNegocios();
        $this->alert('success', 'El negocio ha sido eliminado');

    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'negocio',
            'nombre_legal',
            'nombre_comercial',
            'ruc',
            'direccion',
            'ubigeo',
            'departamento',
            'provincia',
            'distrito',
            'codigo_pais',
            'urbanizacion',
            'tipo_negocio',
            'usuario_sol',
            'clave_sol',
            'client_secret',
            'modo',
            'certificado',
            'logo_factura',
            'certificado_actual',
            'logo_actual',
            'mostrarFormularioNegocio',
            'isEditing',
            'nuevaClave',
            'nuevoValor',
            'nuevaUbicacion',
            'activeTab'
        ]);

        $this->infoAdicional = [
            'Cabecera' => [],
            'Centro' => [],
            'Pie' => [],
        ];

        $this->resetValidation();
    }


    public function render()
    {
        return view('livewire.dueno_tienda.negocio_panel.gestion-negocios');
    }
}

