<?php

namespace App\Traits;

use App\Models\Negocio;

trait ConNegocioSeleccionado
{
    /**
     * Instancia del negocio seleccionado.
     *
     * @var \App\Models\Negocio|null
     */
    public $negocioSeleccionado;

    /**
     * Inicializa el negocio seleccionado desde la sesión.
     * Debe llamarse en el método mount() del componente.
     *
     * @return void
     */
    public function cargarNegocioSeleccionado()
    {
        $uuid = session('negocio_actual_uuid');

        if (!$uuid) {
            $this->negocioSeleccionado = null;
            return;
        }

        $this->negocioSeleccionado = Negocio::where('uuid', $uuid)->first();
    }

    /**
     * Devuelve el negocio seleccionado (helper directo).
     *
     * @return \App\Models\Negocio|null
     */
    public function negocio()
    {
        if (!$this->negocioSeleccionado) {
            $this->cargarNegocioSeleccionado();
        }

        return $this->negocioSeleccionado;
    }

    /**
     * Verifica si hay un negocio activo en sesión.
     *
     * @return bool
     */
    public function tieneNegocioSeleccionado()
    {
        return !is_null($this->negocio());
    }
}
