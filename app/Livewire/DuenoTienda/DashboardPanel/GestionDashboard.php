<?php

namespace App\Livewire\DuenoTienda\DashboardPanel;
use App\Services\Ventas\PosServicio;
use App\Traits\LivewireAlerta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;


class GestionDashboard extends Component
{
    use LivewireAlerta;
    
    #region Variables
    public $filtros = [];
    public $filtro = null;
    public $data;
    public $tarjetas_estadisticas = [];
    public $ventas_semanales = [];
    public $alertas_inventario = [];
    #endregion
    public function updatedFiltro($filtro)
    {
        $this->obtenerDatos($filtro);
    }
    #region Base
    public function mount()
    {
        $user = auth()->user();

        $this->filtros = PosServicio::obtenerFiltros($user);

        if (!empty($this->filtros)) {
            $this->filtro = $this->filtros[0]['value'];
            $this->obtenerDatos($this->filtro);
        }
    }
    public function render()
    {
        return view('livewire.dueno_tienda.dashboard_panel.gestion-dashboard');
    }
    #endregion
    #region Metodos
    private function obtenerDatos($filtro)
    {
        try {
            $this->data = PosServicio::obtenerEstadisticas($filtro);
            
            $this->tarjetas_estadisticas = $this->data['tarjetas_estadisticas'];
            $this->ventas_semanales = $this->data['ventas_semanales'];
            $this->alertas_inventario = $this->data['alertas_inventario'];
            $this->alert('success', 'Información recopilada');
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
    #endregion
    
}