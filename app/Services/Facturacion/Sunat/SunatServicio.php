<?php

namespace App\Services\Facturacion\Sunat;

use GuzzleHttp\Client;
use Log;

class SunatServicio
{
    public static function consultarPorRuc(string $ruc): ?array
    {
        $token = config('services.sunat.token', 'abcxyz'); // Puedes mover el token a .env
        $url = "https://apisunat.perudevsolutions.com/public/api/v1/ruc/{$ruc}?token={$token}";

        $client = new Client();
        $response = $client->get($url);
        $data = json_decode($response->getBody(), true);

        return [
            'ruc' => $data['ruc'] ?? null,
            'razonSocial' => $data['razonSocial'] ?? null,
            'nombreComercial' => $data['nombreComercial'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'departamento' => $data['departamento'] ?? null,
            'provincia' => $data['provincia'] ?? null,
            'distrito' => $data['distrito'] ?? null,
            'telefonos' => $data['telefonos'] ?? [],
            'estado' => $data['estado'] ?? null,
            'condicion' => $data['condicion'] ?? null,
        ];
    }
}