<?php

namespace App\Traits;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

trait LivewireAlerta
{

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
    public function alertModal(string $type = 'success', string $title = '', string $message = '')
    {
        $alert = LivewireAlert::title($title)
            ->text($message)
            ->withConfirmButton('Cerrar')
            ->timer(0);

        switch ($type) {
            case 'error':
                $alert->error();
                break;

            case 'warning':
                $alert->warning();
                break;

            case 'info':
                $alert->info();
                break;

            case 'success':
            default:
                $alert->success();
                break;
        }

        $alert->show();
    }
}

