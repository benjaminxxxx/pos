<?php

namespace App\Traits\Sunat;

use App\Models\SunatCatalogo7;

trait AfectacionesIgvTrait
{
    public function getAfectacionesIgv()
    {
        return SunatCatalogo7::all();
    }
}
