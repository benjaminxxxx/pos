<?php
// app/Services/Negocio/NegocioServicio.php
namespace App\Services\Negocio;

use App\Models\Negocio;
use App\Models\InformacionAdicional;
use App\Models\Sucursal;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class NegocioServicio
{
    public static function porNegocio(int $negocioId)
    {
        return Sucursal::where('negocio_id', $negocioId)
            ->with(['correlativos'])
            ->orderBy('nombre');
    }
    public static function guardar(array $data, ?Negocio $negocioExistente = null): Negocio
    {
        return DB::transaction(function () use ($data, $negocioExistente) {

            $isEditing = $negocioExistente !== null;
            $negocio = $isEditing ? $negocioExistente : new Negocio();

            // Propietario: cuenta, no user
            if (!$isEditing) {
                $cuenta = Auth::user()->cuenta;
                if (!$cuenta) {
                    throw new Exception("El usuario no tiene una cuenta asociada.");
                }
                $negocio->cuenta_id = $cuenta->id;
            }

            // Datos básicos
            $negocio->nombre_legal = $data['nombre_legal'];
            $negocio->nombre_comercial = $data['nombre_comercial'];
            $negocio->ruc = $data['ruc'];
            $negocio->direccion = $data['direccion'];
            $negocio->ubigeo = $data['ubigeo'] ?? null;
            $negocio->departamento = $data['departamento'] ?? null;
            $negocio->provincia = $data['provincia'] ?? null;
            $negocio->distrito = $data['distrito'] ?? null;
            $negocio->codigo_pais = $data['codigo_pais'] ?? 'PE';
            $negocio->urbanizacion = $data['urbanizacion'] ?? null;
            $negocio->tipo_negocio = $data['tipo_negocio'] ?? null;
            $negocio->usuario_sol = $data['usuario_sol'] ?? null;
            $negocio->clave_sol = $data['clave_sol'] ?? null;
            $negocio->client_secret = $data['client_secret'] ?? null;
            $negocio->modo = $data['modo'] ?? 'demo';

            // Manejo de logo
            if (!empty($data['logo_factura_a_eliminar']) && ($data['logo_factura_eliminada'] ?? false)) {
                Storage::disk('public')->delete($data['logo_factura_a_eliminar']);
                $negocio->logo_factura = null;
            }
            if (!empty($data['logo_factura'])) {
                $negocio->logo_factura = self::guardarArchivo($data['logo_factura'], 'logos');
            }

            // Manejo de certificado
            if (!empty($data['certificado_a_eliminar']) && ($data['certificado_eliminada'] ?? false)) {
                Storage::disk('public')->delete($data['certificado_a_eliminar']);
                $negocio->certificado = null;
            }
            if (!empty($data['certificado'])) {
                $negocio->certificado = self::guardarArchivo($data['certificado'], 'certificados');
            }

            $negocio->save();

            // Información adicional
            if ($isEditing) {
                InformacionAdicional::where('negocio_id', $negocio->id)->delete();
            }

            self::guardarInformacionAdicional($negocio->id, $data['info_cabecera'] ?? [], 'Cabecera');
            self::guardarInformacionAdicional($negocio->id, $data['info_centro'] ?? [], 'Centro');
            self::guardarInformacionAdicional($negocio->id, $data['info_pie'] ?? [], 'Pie');

            return $negocio;
        });
    }

    private static function guardarArchivo($file, $type)
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

            // Asegúrate de que el directorio exista
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }

            $fullPath = Storage::disk('public')->path("{$path}/{$fileName}");

            if ($type === 'logos') {
                // Redimensionar si es logo
                $image = Image::read($file)->scale(500);
                $image->save($fullPath);
            } else {
                // Guardar directamente
                $file->storeAs($path, $fileName, 'public');
            }

            return "{$path}/{$fileName}";
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    private static function guardarInformacionAdicional($negocioId, $items, $ubicacion)
    {
        foreach ($items as $item) {
            if (empty($item['valor']))
                continue;
            InformacionAdicional::create([
                'negocio_id' => $negocioId,
                'clave' => $item['clave'],
                'valor' => $item['valor'],
                'ubicacion' => $ubicacion
            ]);
        }
    }
}