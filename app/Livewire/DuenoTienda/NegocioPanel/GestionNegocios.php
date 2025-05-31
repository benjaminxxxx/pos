<?php

namespace App\Livewire\DuenoTienda\NegocioPanel;

use App\Models\Negocio;
use App\Models\InformacionAdicional;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class GestionNegocios extends Component
{
    use WithFileUploads;

    public $negocios;
    public $negocio;
    public $showForm = false;
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
    public $logo_factura;

    // Para mostrar nombres de archivos actuales
    public $certificado_actual;
    public $logo_actual;

    // Información adicional
    public $infoAdicionalCabecera = [];
    public $infoAdicionalCentro = [];
    public $infoAdicionalPie = [];

    // Campos temporales para agregar nueva información
    public $nuevaClave = '';
    public $nuevoValor = '';
    public $nuevaUbicacion = 'Cabecera';
    public $tiposNegocio = [];

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
        'logo_factura' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
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

    public function render()
    {
        return view('livewire.dueno_tienda.negocio_panel.gestion-negocios');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
        $this->activeTab = 'general';
    }

    public function edit(string $uuid)
    {
        $negocio = Negocio::where('uuid', $uuid)->first();
        if (!$negocio) {
            LivewireAlert::text('El negocio ya no existe')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
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
        $this->certificado_actual = $negocio->certificado ? 'Certificado actual' : null;
        $this->logo_actual = $negocio->logo_factura ? 'Logo actual' : null;

        // Cargar información adicional
        $this->cargarInformacionAdicional($negocio->id);

        $this->showForm = true;
        $this->isEditing = true;
        $this->activeTab = 'general';
    }

    public function cargarInformacionAdicional($negocioId)
    {
        // Limpiar arrays
        $this->infoAdicionalCabecera = [];
        $this->infoAdicionalCentro = [];
        $this->infoAdicionalPie = [];

        // Cargar información desde la base de datos
        $informacionAdicional = InformacionAdicional::where('negocio_id', $negocioId)->get();

        foreach ($informacionAdicional as $info) {
            switch ($info->ubicacion) {
                case 'Cabecera':
                    $this->infoAdicionalCabecera[] = [
                        'id' => $info->id,
                        'clave' => $info->clave,
                        'valor' => $info->valor
                    ];
                    break;
                case 'Centro':
                    $this->infoAdicionalCentro[] = [
                        'id' => $info->id,
                        'clave' => $info->clave,
                        'valor' => $info->valor
                    ];
                    break;
                case 'Pie':
                    $this->infoAdicionalPie[] = [
                        'id' => $info->id,
                        'clave' => $info->clave,
                        'valor' => $info->valor
                    ];
                    break;
            }
        }
    }

    public function agregarInformacionAdicional()
    {
        // Validar
        $this->validate([
            'nuevaClave' => 'required|string|max:255',
            'nuevoValor' => 'required|string',
            'nuevaUbicacion' => 'required|in:Cabecera,Centro,Pie'
        ]);

        // Agregar a la colección correspondiente
        $nuevoItem = [
            'id' => null, // Será null porque aún no está en la base de datos
            'clave' => $this->nuevaClave,
            'valor' => $this->nuevoValor
        ];

        switch ($this->nuevaUbicacion) {
            case 'Cabecera':
                $this->infoAdicionalCabecera[] = $nuevoItem;
                break;
            case 'Centro':
                $this->infoAdicionalCentro[] = $nuevoItem;
                break;
            case 'Pie':
                $this->infoAdicionalPie[] = $nuevoItem;
                break;
        }

        // Limpiar campos
        $this->nuevaClave = '';
        $this->nuevoValor = '';
    }

    public function eliminarInformacionAdicional($ubicacion, $index)
    {
        switch ($ubicacion) {
            case 'Cabecera':
                array_splice($this->infoAdicionalCabecera, $index, 1);
                break;
            case 'Centro':
                array_splice($this->infoAdicionalCentro, $index, 1);
                break;
            case 'Pie':
                array_splice($this->infoAdicionalPie, $index, 1);
                break;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->isEditing) {
                $negocio = $this->negocio;
            } else {
                $negocio = new Negocio();
                $negocio->user_id = Auth::id();
            }

            $negocio->nombre_legal = $this->nombre_legal;
            $negocio->nombre_comercial = $this->nombre_comercial;
            $negocio->ruc = $this->ruc;
            $negocio->direccion = $this->direccion;
            $negocio->ubigeo = $this->ubigeo;
            $negocio->departamento = $this->departamento;
            $negocio->provincia = $this->provincia;
            $negocio->distrito = $this->distrito;
            $negocio->codigo_pais = $this->codigo_pais;
            $negocio->urbanizacion = $this->urbanizacion;            
            $negocio->tipo_negocio = $this->tipo_negocio;
            $negocio->usuario_sol = $this->usuario_sol;
            $negocio->clave_sol = $this->clave_sol;
            $negocio->client_secret = $this->client_secret;
            $negocio->modo = $this->modo;

            // Procesar archivos si se han subido
            if ($this->certificado) {
                // Elimina el anterior si existe
                if ($negocio->certificado && Storage::disk('public')->exists($negocio->certificado)) {
                    Storage::disk('public')->delete($negocio->certificado);
                }

                // Guarda el nuevo archivo
                $certificadoPath = $this->storeFile($this->certificado, 'certificados');
                $negocio->certificado = $certificadoPath;
            }

            if ($this->logo_factura) {
                // Elimina el anterior si existe
                if ($negocio->logo_factura && Storage::disk('public')->exists($negocio->logo_factura)) {
                    Storage::disk('public')->delete($negocio->logo_factura);
                }

                // Guarda el nuevo archivo
                $logoPath = $this->storeFile($this->logo_factura, 'logos');
                $negocio->logo_factura = $logoPath;
            }


            $negocio->save();

            // Guardar información adicional
            if ($this->isEditing) {
                // Eliminar información adicional existente
                InformacionAdicional::where('negocio_id', $negocio->id)->delete();
            }

            // Guardar nueva información adicional
            $this->guardarInformacionAdicional($negocio->id, $this->infoAdicionalCabecera, 'Cabecera');
            $this->guardarInformacionAdicional($negocio->id, $this->infoAdicionalCentro, 'Centro');
            $this->guardarInformacionAdicional($negocio->id, $this->infoAdicionalPie, 'Pie');

            DB::commit();

            LivewireAlert::text($this->isEditing ? 'Negocio actualizado correctamente' : 'Negocio creado correctamente')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

            $this->resetForm();
            $this->loadNegocios();

        } catch (\Exception $e) {
            DB::rollBack();

            LivewireAlert::text('Error al guardar el negocio: ' . $e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    private function guardarInformacionAdicional($negocioId, $items, $ubicacion)
    {
        foreach ($items as $item) {
            InformacionAdicional::create([
                'negocio_id' => $negocioId,
                'clave' => $item['clave'],
                'valor' => $item['valor'],
                'ubicacion' => $ubicacion
            ]);
        }
    }

    public function delete(string $uuid)
    {
        $negocio = Negocio::where('uuid', $uuid)->first();
        if (!$negocio) {
            LivewireAlert::text('El negocio ya no existe')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        try {
            DB::beginTransaction();

            // Eliminar información adicional
            InformacionAdicional::where('negocio_id', $negocio->id)->delete();

            // Eliminar archivos asociados
            if ($negocio->certificado) {
                Storage::delete($negocio->certificado);
            }

            if ($negocio->logo_factura) {
                Storage::delete($negocio->logo_factura);
            }

            $negocio->delete();

            DB::commit();

            LivewireAlert::text('Negocio eliminado correctamente')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

            $this->loadNegocios();

        } catch (\Exception $e) {
            DB::rollBack();

            LivewireAlert::text('Error al eliminar el negocio: ' . $e->getMessage())
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
            'showForm',
            'isEditing',
            'infoAdicionalCabecera',
            'infoAdicionalCentro',
            'infoAdicionalPie',
            'nuevaClave',
            'nuevoValor',
            'nuevaUbicacion',
            'activeTab'
        ]);

        $this->resetValidation();
    }

    private function storeFile($file, $type)
    {
        try {
            $user = Auth::user();
            $userSlug = Str::slug($user->uuid);
            $year = date('Y');
            $month = date('m');
            $randomId = Str::random(32);
            $extension = $file->getClientOriginalExtension();

            $fileName = "{$randomId}.{$extension}";
            $path = "{$userSlug}/{$year}/{$month}/{$type}";

            $file->storeAs($path, $fileName, 'public');

            return "{$path}/{$fileName}";
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}

