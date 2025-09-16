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
        // ✨ Solución: Obtenemos el servicio aquí
        $servicio = resolve(DisenioImpresionServicio::class);
       
        $this->disenosDisponibles = $servicio->getDisenosDisponibles($tipoComprobanteCodigo);
 
        $existente = $servicio->getConfiguracion(
            $this->selectedNegocio,
            $this->selectedSucursal,
            $tipoComprobanteCodigo
        );
        
        if ($existente) {
            $this->selectedDiseno = $existente->disenio_id;
        }else{
            $this->selectedDiseno = 'default';
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
            
            // ✨ Solución: Obtenemos el servicio aquí
            $servicio = resolve(DisenioImpresionServicio::class);
            $servicio->guardarConfiguracion([
                'negocio_id' => $this->selectedNegocio,
                'sucursal_id' => $this->selectedSucursal,
                'tipo_comprobante_codigo' => $this->selectedTipoComprobante,
                'disenio_id' => $disenioSeleccionado,
            ]);

            $this->selectedDiseno = $disenioSeleccionado;

            $this->alert('success', 'Configuración guardada exitosamente ✅');
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dueno_tienda.configuracion.configuracion_disenio_impresion');
    }
}