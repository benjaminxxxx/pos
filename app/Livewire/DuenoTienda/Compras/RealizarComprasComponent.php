<?php

namespace App\Livewire\DuenoTienda\Compras;

use App\Services\Compra\CompraServicio;
use App\Traits\DatosUtiles\ConProductos;
use App\Traits\DatosUtiles\ConProveedores;
use App\Traits\DatosUtiles\ConSucursales;
use App\Traits\LivewireAlerta;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use DB;

class RealizarComprasComponent extends Component
{
    use ConProveedores, ConSucursales, ConProductos, LivewireAlerta;

    // Propiedades de la Cabecera de la Compra
    public $cuentaId = 1; // Asumiendo ID de cuenta fijo para esta demo.
    public $tipoComprobante = 'FACTURA'; // Ajustado a mayúsculas para el servicio
    public $numeroComprobante;
    public $fechaComprobante;
    public $fechaVencimiento; // Nuevo
    public $glosaObservacion; // Nuevo (Usando el nombre de la DB)
    public $formaPago = 'CONTADO';
    public $moneda = 'PEN'; // Nuevo
    public $tipoCambio = 1.0000; // Nuevo (Default 1 para PEN)
    public $estadoPago = 'PENDIENTE'; // Nuevo

    public $proveedorSeleccionado;
    public $sucursalSeleccionada;

    // Detalle y Totales
    public $productosAgregados = [];
    public $subtotal = 0; // Base imponible
    public $igv = 0;
    public $total = 0; // Total pagado (Suma del campo total de la línea, que incluye IGV)

    protected $listeners = ['recalcularTotales' => 'calcularTotales'];

    public function mount()
    {
        // Establecer la fecha actual como valor por defecto
        $this->fechaComprobante = now()->toDateString();

        if ($this->sucursales->count() > 0) {
            $this->sucursalSeleccionada = $this->sucursales->first()->id;
        }
    }

    /**
     * Recalcula los totales a partir de los detalles agregados.
     * La lógica aquí ASUME que costo_unitario es el precio FINAL (Incluido IGV).
     */
    public function calcularTotales()
    {
        $baseImponibleTotal = 0;
        $igvTotal = 0;
        $montoTotalPagar = 0; // Total de la factura

        foreach ($this->productosAgregados as $p) {
            $cantidad = (float) $p['cantidad'];
            $costoUnitarioTotal = (float) $p['costo_unitario']; // Precio final incluido IGV
            $descPorcentaje = (float) ($p['descuento_porcentaje'] ?? 0);
            $igvPorcentaje = (float) ($p['porcentaje_igv'] ?? 0) / 100;
            
            // 1. Calcular el Costo Unitario NETO (Base Imponible por unidad)
            $factor = 1 + $igvPorcentaje;
            $baseImponibleUnitario = $igvPorcentaje > 0 ? ($costoUnitarioTotal / $factor) : $costoUnitarioTotal;
            
            // 2. Subtotal Bruto (Base Imponible sin descuento)
            $subtotalBruto = $baseImponibleUnitario * $cantidad;
            
            // 3. Descuento aplicado a la Base Imponible
            $descuentoMonto = $subtotalBruto * ($descPorcentaje / 100);
            
            // 4. Subtotal Neto (Base Imponible con descuento)
            $neto = $subtotalBruto - $descuentoMonto;
            
            // 5. IGV
            $igvMonto = $neto * $igvPorcentaje;
            
            // 6. Total de la línea (Neto + IGV)
            $totalLinea = $neto + $igvMonto;

            // Acumular totales
            $baseImponibleTotal += $neto;
            $igvTotal += $igvMonto;
            $montoTotalPagar += $totalLinea;
        }

        $this->subtotal = round($baseImponibleTotal, 2);
        $this->igv = round($igvTotal, 2);
        $this->total = round($montoTotalPagar, 2);
    }

    /**
     * Llama al servicio para registrar la compra.
     * @param CompraServicio $servicio
     */
    public function guardarCompra(CompraServicio $servicio)
    {
        // 1. Recalcular y validar totales
        $this->calcularTotales();

        $detallesFormateados = array_map(function ($detalle) {
            // Recalculamos el costo neto para el detalle de la base de datos
            $igvPorcentaje = (float) ($detalle['porcentaje_igv'] ?? 0) / 100;
            $factor = 1 + $igvPorcentaje;
            // El costo_unitario que se guarda en la base de datos debe ser la base imponible por unidad.
            $costoUnitarioNeto = $igvPorcentaje > 0 ? ((float) $detalle['costo_unitario'] / $factor) : (float) $detalle['costo_unitario'];
            
            return [
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'costo_unitario' => round($costoUnitarioNeto, 4), // Guardar el costo unitario NETO
                'unidad_medida' => $detalle['unidad_medida'],
                'factor_conversion' => $detalle['factor_conversion'],
                'descuento_porcentaje' => $detalle['descuento_porcentaje'],
                'tipo_igv' => $detalle['tipo_igv'],
                'porcentaje_igv' => $detalle['porcentaje_igv'],
            ];
        }, $this->productosAgregados);
        
        $data = [
            // Cabecera
            'cuenta_id' => $this->cuentaId,
            'sucursal_id' => $this->sucursalSeleccionada,
            'proveedor_id' => $this->proveedorSeleccionado,
            
            // Comprobante
            'tipo_comprobante' => strtoupper($this->tipoComprobante),
            'numero_comprobante' => $this->numeroComprobante,
            'forma_pago' => $this->formaPago,
            'fecha_comprobante' => $this->fechaComprobante,
            'fecha_vencimiento' => $this->fechaVencimiento,
            'glosa_o_observacion' => $this->glosaObservacion,

            // Finanzas
            'moneda' => $this->moneda,
            'tipo_cambio' => $this->tipoCambio,
            'subtotal' => $this->subtotal, // Base imponible total
            'igv' => $this->igv,
            'total' => $this->total, // Total de la factura
            'estado_pago' => $this->estadoPago,
            
            // Detalles (ya contienen el costo unitario neto)
            'detalles' => $detallesFormateados,
        ];
        
        try {
            DB::beginTransaction();
            $compra = $servicio->registrarCompra($data);
            DB::commit();

            $this->alert('success',"Compra N° {$compra->id} registrada con éxito.");
            $this->reset(['productosAgregados', 'numeroComprobante', 'proveedorSeleccionado']);
            $this->calcularTotales();

        } catch (ValidationException $e) {
            DB::rollBack();
            $this->alert('error', 'Verifique los datos ingresados: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error',$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dueno_tienda.compras.realizar-compras-component');
    }
}
