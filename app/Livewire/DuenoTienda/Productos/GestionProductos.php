<?php

namespace App\Livewire\DuenoTienda\Productos;

use App\Models\CategoriaProducto;
use App\Models\Marca;
use App\Models\Sucursal;
use App\Services\ProductoServicio;
use App\Traits\LivewireAlerta;
use App\Traits\SeleccionaNegocio;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Traits\Sunat\AfectacionesIgvTrait;
use App\Traits\Sunat\UnidadesTrait;


class GestionProductos extends Component
{
    use WithPagination, WithFileUploads, SeleccionaNegocio, AfectacionesIgvTrait, UnidadesTrait, LivewireAlerta;

    // ------------------------------
    // Filtros y búsqueda
    // ------------------------------
    public $search = '';
    public $categoriaFilter = '';
    public $marcaFilter = '';
    public $activoFilter = '';

    // ------------------------------
    // Control del modal y selección
    // ------------------------------
    public $isOpen = false;
    public $producto_id;
    public $uuid;

    // ------------------------------
    // Datos del producto
    // ------------------------------
    public $codigo_barra;
    public $sunat_code;
    public $descripcion;
    public $detalle;     // antes descripcion extendida
    public $tipo_afectacion_igv; // sigue igual

    public $porcentaje_igv = 18; // antes igv
    public $monto_venta; // antes precio_base
    public $monto_venta_sinigv;
    public $monto_compra; // antes precio_compra
    public $monto_compra_sinigv;
    public $precio_mayorista;
    public $minimo_mayorista;

    public $categoria_id;
    public $marca_id;
    public $negocio_id;
    public $unidad;
    public $activo = true;

    // ------------------------------
    // Imagen
    // ------------------------------
    public $imagen;
    public $imagen_url; // Para imagen ya guardada
    public $imagen_eliminada = false;
    public $imagen_a_eliminar = null;

    // ------------------------------
    // Relaciones
    // ------------------------------
    public $presentaciones = [];
    public $stocks = [];
    public $sucursales = [];

    // ------------------------------
    // Datos auxiliares
    // ------------------------------
    public $afectaciones = [];
    public $unidades = [];
    public $aplicaIgv = true;

    public function mount()
    {
        // Inicializar la selección de negocio
        $this->mountSeleccionaNegocio();
        $this->afectaciones = $this->getAfectacionesIgv();
        $this->unidades = $this->getUnidades();
        $this->unidad = $this->getUnidadPreseleccionada();

        // Si hay un negocio seleccionado, cargar sus sucursales
        if ($this->negocioSeleccionado) {
            $this->negocio_id = $this->negocioSeleccionado->id;
            $this->cargarSucursales();
        }
    }
    public function updatedTipoAfectacionIgv()
    {
        $this->igv = null;
        //$this->afectaciones (codigo,aplica_igv (boolean))
        $afectacion = collect($this->afectaciones)->firstWhere('codigo', $this->tipo_afectacion_igv);
        if ($afectacion) {
            $this->aplicaIgv = $afectacion['aplica_igv'];
        } else {
            $this->aplicaIgv = false; // Valor por defecto si no se encuentra la afectación
        }
    }
    public function resetearComponente()
    {
        // Este método se llama cuando se cambia de negocio
        $this->reset(['search', 'categoriaFilter', 'marcaFilter', 'activoFilter']);
        $this->resetPage();

        if ($this->negocioSeleccionado) {
            $this->negocio_id = $this->negocioSeleccionado->id;
            $this->cargarSucursales();
        }
    }

    public function cargarSucursales()
    {
        if ($this->negocio_id) {
            $this->sucursales = Sucursal::where('negocio_id', $this->negocio_id)->get();
            $this->stocks = [];

            foreach ($this->sucursales as $sucursal) {
                $this->stocks[$sucursal->id] = [
                    'cantidad' => 0,
                    'stock_minimo' => 0,
                ];
            }
        }
    }

    public function render()
    {
        if (!$this->negocioSeleccionado) {
            return view('livewire.dueno_tienda.productos.gestion-productos', [
                'productos' => collect(),
                'categorias' => collect(),
                'marcas' => collect()
            ]);
        }

        $productos = ProductoServicio::buscar([
            'negocio_id' => $this->negocioSeleccionado->id,
            'search' => $this->search,
            'categoria_id' => $this->categoriaFilter,
            'marca_id' => $this->marcaFilter,
            'activo' => $this->activoFilter,
        ]);

        $categorias = CategoriaProducto::where(function ($query) {
            $query->whereNull('tipo_negocio')
                ->orWhere('tipo_negocio', $this->negocioSeleccionado->tipo_negocio);
        })->get();

        $marcas = Marca::where(function ($query) {
            $query->whereNull('tipo_negocio')
                ->orWhere('tipo_negocio', $this->negocioSeleccionado->tipo_negocio);
        })->get();

        return view('livewire.dueno_tienda.productos.gestion-productos', [
            'productos' => $productos,
            'categorias' => $categorias,
            'marcas' => $marcas
        ]);
    }

    public function resetFormulario()
    {
        $this->resetValidation();

        $this->reset([
            'producto_id',
            'uuid',
            'codigo_barra',
            'sunat_code',
            'descripcion',
            'detalle',            // ← antes era descripcion
            'imagen',
            'imagen_url',
            'porcentaje_igv',     // ← antes era igv
            'monto_venta',        // ← antes era precio_base
            'monto_venta_sinigv',
            'monto_compra',       // ← antes era precio_compra
            'monto_compra_sinigv',
            'precio_mayorista',
            'minimo_mayorista',
            'categoria_id',
            'marca_id',
            'negocio_id',
            'stocks',
        ]);
        $this->negocio_id = $this->negocioSeleccionado ? $this->negocioSeleccionado->id : null;
        $this->tipo_afectacion_igv = $this->afectaciones[0]['codigo'] ?? null; // Preseleccionar la primera afectación
        $this->aplicaIgv = $this->afectaciones[0]['aplica_igv'] ?? true; // Preseleccionar el primer valor de aplica_igv
        $this->unidad = $this->getUnidadPreseleccionada();
        $this->activo = true;
        $this->presentaciones = [];
        $this->cargarSucursales();
    }

    public function create()
    {
        $this->resetFormulario();
        $this->isOpen = true;
    }

    public function edit($uuid)
    {
        $this->resetFormulario();
        try {
            $producto = ProductoServicio::obtenerProductoPorUuid($uuid);
            $this->producto_id = $producto->id;
            $this->uuid = $producto->uuid;
            $this->codigo_barra = $producto->codigo_barra;
            $this->sunat_code = $producto->sunat_code;
            $this->descripcion = $producto->descripcion;
            $this->detalle = $producto->detalle;
            $this->imagen_url = $producto->imagen_path;
            $this->imagen_a_eliminar = null;
            $this->imagen_eliminada = false;
            $this->imagen = null; // Reseteamos la imagen para evitar conflictos

            $this->tipo_afectacion_igv = $producto->tipo_afectacion_igv;
            $this->porcentaje_igv = $producto->porcentaje_igv;
            $this->monto_venta = $producto->monto_venta;
            $this->monto_venta_sinigv = $producto->monto_venta_sinigv;
            $this->monto_compra = $producto->monto_compra;
            $this->monto_compra_sinigv = $producto->monto_compra_sinigv;
            $this->precio_mayorista = $producto->precio_mayorista;
            $this->minimo_mayorista = $producto->minimo_mayorista;
            $this->unidad = $producto->unidad;

            $this->categoria_id = $producto->categoria_id;
            $this->marca_id = $producto->marca_id;
            $this->negocio_id = $producto->negocio_id;
            $this->activo = $producto->activo;

            // Cargar presentaciones (si las manejas aún)
            $this->presentaciones = $producto->presentaciones()->get()->toArray();

            // Cargar stocks
            $this->cargarSucursales();
            foreach ($producto->stocks as $stock) {
                if (isset($this->stocks[$stock->sucursal_id])) {
                    $this->stocks[$stock->sucursal_id]['cantidad'] = $stock->cantidad;
                    $this->stocks[$stock->sucursal_id]['stock_minimo'] = $stock->stock_minimo;
                }
            }

            $this->isOpen = true;
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function store()
    {
        $this->validate([
            'codigo_barra' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('productos', 'codigo_barra')->ignore($this->producto_id),
            ],
            'sunat_code' => 'nullable|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'detalle' => 'nullable|string',
            //'imagen' => 'nullable|image|max:1024',
            'porcentaje_igv' => 'required|numeric|min:0|max:20',
            'tipo_afectacion_igv' => 'required|exists:sunat_catalogo_7,codigo',
            'monto_venta' => 'required|numeric|min:0',
            'monto_compra' => 'required|numeric|min:0',
            'unidad' => 'required|string|max:5',
            'categoria_id' => 'nullable|exists:categorias_productos,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'negocio_id' => 'required|exists:negocios,id',
            'activo' => 'boolean',
            'stocks.*.cantidad' => 'required|numeric|min:0',
            'stocks.*.stock_minimo' => 'required|numeric|min:0',
        ]);

        try {

            $data = [
                'producto_id' => $this->producto_id,
                'codigo_barra' => $this->codigo_barra,
                'sunat_code' => $this->sunat_code,
                'descripcion' => $this->descripcion,
                'detalle' => $this->detalle,
                'imagen' => $this->imagen,
                'imagen_url'=> $this->imagen_url,
                'imagen_eliminada' => $this->imagen_eliminada,
                'imagen_a_eliminar' => $this->imagen_a_eliminar,
                'porcentaje_igv' => $this->porcentaje_igv,
                'monto_venta' => $this->monto_venta,
                'monto_compra' => $this->monto_compra,
                'precio_mayorista' => $this->precio_mayorista,
                'minimo_mayorista' => $this->minimo_mayorista,
                'unidad' => $this->unidad,
                'tipo_afectacion_igv' => $this->tipo_afectacion_igv,
                'categoria_id' => $this->categoria_id,
                'marca_id' => $this->marca_id,
                'negocio_id' => $this->negocio_id,
                'activo' => $this->activo,
                'stocks' => $this->stocks,
                'presentaciones' => $this->presentaciones,
            ];
            
            ProductoServicio::guardar($data);

            $this->alert('success', $this->producto_id ? 'Producto actualizado correctamente.' : 'Producto creado correctamente.');
            $this->closeModal();
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function delete($uuid)
    {
        try {
            ProductoServicio::eliminarProductoPorUuid($uuid);
            $this->alert('success', 'Producto eliminado correctamente.');
        } catch (\Illuminate\Validation\UnauthorizedException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->alert('error', 'Producto no encontrado.');
        } catch (\Exception $e) {
            $this->alert('error', 'Error inesperado al eliminar el producto.');
        }
    }
    public function eliminarImagen()
    {
        $this->imagen = null;        
        $this->imagen_a_eliminar = $this->imagen_url;
        $this->imagen_url = null;
        $this->imagen_eliminada = true;
    }

    public function addPresentacion()
    {
        $this->presentaciones[] = [
            'codigo_barra' => '',
            'unidad' => $this->getUnidadPreseleccionada(),
            'descripcion' => '',
            'factor' => 1,
            'precio' => 0,
            'precio_mayorista'=>null,
            'minimo_mayorista'=>null,
            'activo' => true
        ];
    }

    public function removePresentacion($index)
    {
        unset($this->presentaciones[$index]);
        $this->presentaciones = array_values($this->presentaciones);
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }
}

