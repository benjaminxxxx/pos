<?php

namespace App\Traits;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

trait LivewireAlerta
{
    public $negocios = [];
    public $negocioSeleccionado = null;
    public $mostrarModalSeleccionNegocio = false;
    public $negocioId = null;

    public function alert(string $type = 'success', string $message = '', array $options = [])
    {
        switch ($type) {
            case 'success':
            default:
                LivewireAlert::text($message)
                    ->success()
                    ->toast()
                    ->position('top-end')
                    ->show();
                break;


            case 'error':
                LivewireAlert::text($message)
                    ->error()
                    ->toast()
                    ->position('top-end')
                    ->show();
                break;
        }

    }
}

