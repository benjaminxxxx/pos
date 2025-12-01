<?php

namespace App\Livewire\DuenoTienda\ConfiguracionPanel;

use App\Traits\LivewireAlerta;
use Livewire\Component;
use App\Services\DisenioImpresionServicio;

class DisenioImpresion extends Component
{
    use LivewireAlerta;

    public $negocios = [];
    public $sucursales = [];
    public $tiposComprobante = [];
    public $disenosDisponibles = [];

    public $selectedNegocio = null;
    public $selectedSucursal = null;
    public $selectedTipoComprobante = null;
    public $selectedDiseno = null;
    public $configForm = [
        'width_mm' => null,
        'height_mm' => null,
        'orientation' => 'portrait',
        'margin_top_mm' => 5,
        'margin_bottom_mm' => 5,
        'margin_left_mm' => 5,
        'margin_right_mm' => 5,
    ];


    // ✨ Ya no es necesario mantener el servicio como una propiedad de la clase
    // protected $servicio;

    public function mount(DisenioImpresionServicio $servicio)
    {
        // El servicio se inyecta aquí y se usa solo para la carga inicial
        $this->negocios = $servicio->getNegocios(auth()->id());
        $this->tiposComprobante = $servicio->getTiposComprobante();
    }

    public function seleccionarNegocio($negocioId)
    {
        $this->selectedNegocio = $negocioId;
        // ✨ Solución: Obtenemos el servicio aquí
        $servicio = resolve(DisenioImpresionServicio::class);
        $this->sucursales = $servicio->getSucursales($negocioId);
        $this->reset(['selectedSucursal', 'selectedTipoComprobante', 'selectedDiseno']);
    }

    public function updatedSelectedSucursal($sucursalId)
    {
        $this->reset(['selectedTipoComprobante', 'selectedDiseno']);
    }

    public function seleccionarTipoComprobante($tipoComprobanteCodigo)
    {
        $this->selectedTipoComprobante = $tipoComprobanteCodigo;

        $this->disenosDisponibles = app(DisenioImpresionServicio::class)->getDisenosDisponibles($tipoComprobanteCodigo);

        $existente = app(DisenioImpresionServicio::class)->getConfiguracion(
            $this->selectedNegocio,
            $this->selectedSucursal,
            $tipoComprobanteCodigo
        );

        if ($existente) {
            $this->selectedDiseno = $existente->disenio_id;
            $this->configForm = [
                'width_mm' => $existente->custom_width_mm,
                'height_mm' => $existente->custom_height_mm,
                'orientation' => $existente->custom_orientation,
                'margin_top_mm' => $existente->custom_margin_top_mm,
                'margin_bottom_mm' => $existente->custom_margin_bottom_mm,
                'margin_left_mm' => $existente->custom_margin_left_mm,
                'margin_right_mm' => $existente->custom_margin_right_mm,
            ];

            return;
        }

        $primerDiseno = $this->disenosDisponibles->first();
        $this->selectedDiseno = 'default';

        if ($primerDiseno) {
            $this->configForm = [
                'width_mm' => $primerDiseno->base_width_mm,
                'height_mm' => $primerDiseno->base_height_mm,
                'orientation' => $primerDiseno->base_orientation ?? 'portrait',
                'margin_top_mm' => $primerDiseno->base_margin_top_mm,
                'margin_bottom_mm' => $primerDiseno->base_margin_bottom_mm,
                'margin_left_mm' => $primerDiseno->base_margin_left_mm,
                'margin_right_mm' => $primerDiseno->base_margin_right_mm
            ];
        } else {
            // Fallback en caso extremo (casi imposible)
            $this->configForm = [
                'width_mm' => null,
                'height_mm' => null,
                'orientation' => 'portrait',
                'margin_top_mm' => 0,
                'margin_bottom_mm' => 0,
                'margin_left_mm' => 0,
                'margin_right_mm' => 0
            ];
        }
    }
    public function seleccionarDiseno($disenioSeleccionado)
    {
        try {
            $this->validate([
                'selectedNegocio' => 'required|integer',
                'selectedSucursal' => 'required|integer',
                'selectedTipoComprobante' => 'required',
            ]);

            // Guardar la configuración (activar/desactivar según la regla definida)
            app(DisenioImpresionServicio::class)->guardarConfiguracion([
                'negocio_id' => $this->selectedNegocio,
                'sucursal_id' => $this->selectedSucursal,
                'disenio_id' => $disenioSeleccionado,
                'tipo_comprobante_codigo' => $this->selectedTipoComprobante,
            ]);

            // Actualizar selección en el front
            $this->selectedDiseno = $disenioSeleccionado;

            // -----------------------------------------------------------
            // PRECARGAR CONFIGURACIÓN SEGÚN EL DISEÑO SELECCIONADO
            // -----------------------------------------------------------

            // Si seleccionó "default", cargar como en seleccionarTipoComprobante()
            if ($disenioSeleccionado === 'default') {
                $primerDiseno = $this->disenosDisponibles->first();

                if ($primerDiseno) {
                    $this->configForm = [
                        'width_mm' => $primerDiseno->base_width_mm,
                        'height_mm' => $primerDiseno->base_height_mm,
                        'orientation' => $primerDiseno->base_orientation ?? 'portrait',
                        'margin_top_mm' => $primerDiseno->base_margin_top_mm,
                        'margin_bottom_mm' => $primerDiseno->base_margin_bottom_mm,
                        'margin_left_mm' => $primerDiseno->base_margin_left_mm,
                        'margin_right_mm' => $primerDiseno->base_margin_right_mm,
                        'mostrar_logo' => true,
                    ];
                }

                $this->alert('success', 'Configuración restablecida a los valores por defecto.');
                return;
            }

            // Buscar si existe configuración personalizada para este diseño
            $existente = \App\Models\DisenioImpresion::where('negocio_id', $this->selectedNegocio)
                ->where('sucursal_id', $this->selectedSucursal)
                ->where('disenio_id', $disenioSeleccionado)
                ->where('activo', true)
                ->first();

            if ($existente) {
                // Cargar valores personalizados
                $this->configForm = [
                    'width_mm' => $existente->custom_width_mm,
                    'height_mm' => $existente->custom_height_mm,
                    'orientation' => $existente->custom_orientation,
                    'margin_top_mm' => $existente->custom_margin_top_mm,
                    'margin_bottom_mm' => $existente->custom_margin_bottom_mm,
                    'margin_left_mm' => $existente->custom_margin_left_mm,
                    'margin_right_mm' => $existente->custom_margin_right_mm,
                ];
            } else {
                // Cargar valores base desde diseños disponibles
                $disenoBase = $this->disenosDisponibles
                    ->firstWhere('id', $disenioSeleccionado);

                if ($disenoBase) {
                    $this->configForm = [
                        'width_mm' => $disenoBase->base_width_mm,
                        'height_mm' => $disenoBase->base_height_mm,
                        'orientation' => $disenoBase->base_orientation ?? 'portrait',
                        'margin_top_mm' => $disenoBase->base_margin_top_mm,
                        'margin_bottom_mm' => $disenoBase->base_margin_bottom_mm,
                        'margin_left_mm' => $disenoBase->base_margin_left_mm,
                        'margin_right_mm' => $disenoBase->base_margin_right_mm,
                    ];
                }
            }

            $this->alert('success', 'Diseño seleccionado y valores precargados.');
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function guardarConfig()
    {
        try {

            // Normalizar valores
            $width = trim($this->configForm['width_mm'] ?? '');
            $height = trim($this->configForm['height_mm'] ?? '');

            // Width y Height → vacíos se vuelven null, pero si tienen valor deben ser > 0
            $custom_width_mm = ($width === '' ? null : (float) $width);
            $custom_height_mm = ($height === '' ? null : (float) $height);

            if (!is_null($custom_width_mm) && $custom_width_mm <= 0) {
                throw new \Exception("El ancho debe ser mayor a 0 mm");
            }

            if (!is_null($custom_height_mm) && $custom_height_mm <= 0) {
                throw new \Exception("La altura debe ser mayor a 0 mm");
            }

            // Márgenes → pueden ser 0, pero '' debe ser null
            $cleanMargin = function ($value) {
                $value = trim($value ?? '');
                return ($value === '') ? null : (float) $value;
            };

            $data = [
                'negocio_id' => $this->selectedNegocio,
                'sucursal_id' => $this->selectedSucursal,
                'disenio_id' => $this->selectedDiseno,

                'custom_width_mm' => $custom_width_mm,
                'custom_height_mm' => $custom_height_mm,
                'custom_orientation' => $this->configForm['orientation'] ?? null,

                'custom_margin_top_mm' => $cleanMargin($this->configForm['margin_top_mm'] ?? null),
                'custom_margin_bottom_mm' => $cleanMargin($this->configForm['margin_bottom_mm'] ?? null),
                'custom_margin_left_mm' => $cleanMargin($this->configForm['margin_left_mm'] ?? null),
                'custom_margin_right_mm' => $cleanMargin($this->configForm['margin_right_mm'] ?? null),
            ];

            app(DisenioImpresionServicio::class)->guardarOpcionesConfiguracion($data);

            $this->alert('success', 'Configuración de diseño actualizada correctamente');

        } catch (\Throwable $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function resetearConfig(){
        //buscar en disenios_disponibles y pasar eso valores a disenios_impresion
        $disenioSeleccionado = $this->selectedDiseno;
        $primerDiseno = $this->disenosDisponibles->firstWhere('id', $disenioSeleccionado);
        if ($primerDiseno) {
            $this->configForm = [
                'width_mm' => $primerDiseno->base_width_mm,
                'height_mm' => $primerDiseno->base_height_mm,
                'orientation' => $primerDiseno->base_orientation ?? 'portrait',
                'margin_top_mm' => $primerDiseno->base_margin_top_mm,
                'margin_bottom_mm' => $primerDiseno->base_margin_bottom_mm,
                'margin_left_mm' => $primerDiseno->base_margin_left_mm,
                'margin_right_mm' => $primerDiseno->base_margin_right_mm,
            ];
        }
    }
    public function render()
    {
        return view('livewire.dueno_tienda.configuracion.configuracion_disenio_impresion');
    }
}