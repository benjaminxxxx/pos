<?php

namespace App\Livewire\DuenoTienda\Productos;

use App\Models\CategoriaProducto;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\Sucursal;
use App\Models\Unidad;
use App\Traits\SeleccionaNegocio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class GestionProductos extends Component
{
    use WithPagination, WithFileUploads, SeleccionaNegocio;

    // Propiedades para la lista
    public $search = '';
    public $categoriaFilter = '';
    public $marcaFilter = '';
    public $activoFilter = '';
    public $isOpen = false;
    
    // Propiedades para el formulario
    public $producto_id;
    public $uuid;
    public $codigo_barra;
    public $sunat_code;
    public $nombre_producto;
    public $descripcion;
    public $imagen;
    public $imagen_url;
    public $imagen_temp_url;
    public $imagen_path;
    public $igv;
    public $precio_base;
    public $precio_compra;
    public $categoria_id;
    public $marca_id;
    public $negocio_id;
    public $tipo_afectacion_igv;
    public $activo = true;
    
    // Propiedades para presentaciones
    public $presentaciones = [];
    
    // Propiedades para stock
    public $stocks = [];
    
    // Propiedades para sucursales
    public $sucursales = [];
    

    public function mount()
    {
        // Inicializar la selección de negocio
        $this->mountSeleccionaNegocio();
        
        // Si hay un negocio seleccionado, cargar sus sucursales
        if ($this->negocioSeleccionado) {
            $this->negocio_id = $this->negocioSeleccionado->id;
            $this->cargarSucursales();
        }
    }
    public function updatedTipoAfectacionIgv(){
        $this->igv = null;
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
        // Si no hay negocio seleccionado, mostrar vista vacía con modal
        if (!$this->negocioSeleccionado) {
            return view('livewire.dueno_tienda.productos.gestion-productos', [
                'productos' => collect(),
                'categorias' => collect(),
                'marcas' => collect(),
                'unidades' => collect(),
            ]);
        }
        
        $productos = Producto::query()
            ->where('negocio_id', $this->negocioSeleccionado->id)
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('nombre_producto', 'like', '%' . $this->search . '%')
                      ->orWhere('codigo_barra', 'like', '%' . $this->search . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoriaFilter, function ($query) {
                return $query->where('categoria_id', $this->categoriaFilter);
            })
            ->when($this->marcaFilter, function ($query) {
                return $query->where('marca_id', $this->marcaFilter);
            })
            ->when($this->activoFilter !== '', function ($query) {
                return $query->where('activo', $this->activoFilter);
            })
            ->orderBy('nombre_producto')
            ->paginate(10);

        $categorias = CategoriaProducto::where(function($query) {
            $query->whereNull('tipo_negocio')
                  ->orWhere('tipo_negocio', $this->negocioSeleccionado->tipo_negocio);
        })->get();
        
        $marcas = Marca::where(function($query) {
            $query->whereNull('tipo_negocio')
                  ->orWhere('tipo_negocio', $this->negocioSeleccionado->tipo_negocio);
        })->get();
        
        $unidades = Unidad::where('activo', true)
            ->where(function($query) {
                $query->whereNull('tipo_negocio')
                      ->orWhere('tipo_negocio', $this->negocioSeleccionado->tipo_negocio);
            })->get();

        return view('livewire.dueno_tienda.productos.gestion-productos', [
            'productos' => $productos,
            'categorias' => $categorias,
            'marcas' => $marcas,
            'unidades' => $unidades,
        ]);
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['producto_id', 'uuid', 'codigo_barra', 'sunat_code', 'nombre_producto', 'descripcion', 'imagen', 'imagen_path', 'igv','tipo_afectacion_igv', 'precio_base', 'precio_compra', 'categoria_id', 'marca_id']);
        $this->activo = true;
        $this->presentaciones = [];
        $this->cargarSucursales();
        $this->isOpen = true;
    }

    public function edit($uuid)
    {
        $this->resetValidation();
        $producto = Producto::where('uuid', $uuid)->firstOrFail();
        
        // Verificar permisos
        if (Auth::user()->hasRole('vendedor') && $producto->creado_por != Auth::id()) {
            if ($producto->tieneVentas()) {
                LivewireAlert::text('No tienes permisos para editar este producto.')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
                return;
            }
        }
        
        $this->producto_id = $producto->id;
        $this->uuid = $producto->uuid;
        $this->codigo_barra = $producto->codigo_barra;
        $this->sunat_code = $producto->sunat_code;
        $this->nombre_producto = $producto->nombre_producto;
        $this->descripcion = $producto->descripcion;
        $this->imagen_path = $producto->imagen_path;
        $this->imagen_url = $producto->imagen_path ? Storage::disk('public')->url($producto->imagen_path) : null;
        
        $this->tipo_afectacion_igv = $producto->tipo_afectacion_igv;
        $this->igv = $producto->igv;
        $this->precio_base = $producto->precio_base;
        $this->precio_compra = $producto->precio_compra;
        $this->categoria_id = $producto->categoria_id;
        $this->marca_id = $producto->marca_id;
        $this->negocio_id = $producto->negocio_id;
        $this->activo = $producto->activo;
        
        // Cargar presentaciones
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
    }

    public function store()
    {
        $this->validate([
            'codigo_barra' => [
                'nullable', 'string', 'max:255',
                Rule::unique('productos', 'codigo_barra')->ignore($this->producto_id)
            ],
            'sunat_code' => 'nullable|string|max:255',
            'nombre_producto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|max:1024',
            'igv' => 'required|min:0|max:20',
            'tipo_afectacion_igv' => 'required',
            'precio_base' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'categoria_id' => 'nullable|exists:categorias_productos,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'negocio_id' => 'required|exists:negocios,id',
            'activo' => 'boolean',
            'presentaciones.*.codigo_barra' => 'nullable|string|max:255',
            'presentaciones.*.unidad_id' => 'required|exists:unidades,id',
            'presentaciones.*.descripcion' => 'required|string|max:255',
            'presentaciones.*.factor' => 'required|numeric|min:0.01',
            'presentaciones.*.precio' => 'required|numeric|min:0',
            'stocks.*.cantidad' => 'required|numeric|min:0',
            'stocks.*.stock_minimo' => 'required|numeric|min:0',
        ],[
            'codigo_barra.unique' => 'Este código de barra ya está registrado.',
            'codigo_barra.max' => 'El código de barra no puede tener más de 255 caracteres.',
            'nombre_producto.required' => 'El nombre del producto es obligatorio.',
            'nombre_producto.max' => 'El nombre del producto no puede tener más de 255 caracteres.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.max' => 'La imagen no debe superar 1MB.',
            'precio_base.required' => 'El precio base es obligatorio.',
            'precio_base.numeric' => 'El precio base debe ser numérico.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_compra.numeric' => 'El precio de compra debe ser numérico.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'marca_id.exists' => 'La marca seleccionada no es válida.',
            'negocio_id.required' => 'El negocio es obligatorio.',
            'negocio_id.exists' => 'El negocio seleccionado no es válido.',
            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',
    
            'presentaciones.*.unidad_id.required' => 'La unidad es obligatoria en cada presentación.',
            'presentaciones.*.unidad_id.exists' => 'La unidad seleccionada en una presentación no es válida.',
            'presentaciones.*.descripcion.required' => 'La descripción de la presentación es obligatoria.',
            'presentaciones.*.descripcion.max' => 'La descripción de la presentación no puede tener más de 255 caracteres.',
            'presentaciones.*.factor.required' => 'El factor de conversión es obligatorio.',
            'presentaciones.*.factor.numeric' => 'El factor de conversión debe ser un número.',
            'presentaciones.*.factor.min' => 'El factor debe ser mayor que cero.',
            'presentaciones.*.precio.required' => 'El precio de la presentación es obligatorio.',
            'presentaciones.*.precio.numeric' => 'El precio de la presentación debe ser numérico.',
    
            'stocks.*.cantidad.required' => 'La cantidad en stock es obligatoria.',
            'stocks.*.cantidad.numeric' => 'La cantidad debe ser numérica.',
            'stocks.*.stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stocks.*.stock_minimo.numeric' => 'El stock mínimo debe ser numérico.',
        ]);
        
        // Procesar imagen si existe
        if ($this->imagen) {
            $year = date('Y');
            $month = date('m');
            $path = "productos/{$year}/{$month}";
            $filename = Str::random(20) . '.' . $this->imagen->getClientOriginalExtension();
            $this->imagen_path = $this->imagen->storeAs($path, $filename, 'public');
        }
        
        if ($this->producto_id) {
            // Actualizar producto
            $producto = Producto::findOrFail($this->producto_id);
            $producto->update([
                'codigo_barra' => $this->codigo_barra,
                'sunat_code' => $this->sunat_code,
                'nombre_producto' => $this->nombre_producto,
                'descripcion' => $this->descripcion,
                'imagen_path' => $this->imagen_path ?? $producto->imagen_path,
                'igv' => $this->igv,
                'tipo_afectacion_igv'=> $this->tipo_afectacion_igv,
                'precio_base' => $this->precio_base,
                'precio_compra' => $this->precio_compra,
                'categoria_id' => $this->categoria_id,
                'marca_id' => $this->marca_id,
                'negocio_id' => $this->negocio_id,
                'activo' => $this->activo,
            ]);
            
            // Actualizar presentaciones
            $producto->presentaciones()->delete();
            foreach ($this->presentaciones as $presentacion) {
                $producto->presentaciones()->create([
                    'codigo_barra' => $presentacion['codigo_barra'] ?? null,
                    'unidad_id' => $presentacion['unidad_id'],
                    'descripcion' => $presentacion['descripcion'],
                    'factor' => $presentacion['factor'],
                    'precio' => $presentacion['precio'],
                    'activo' => true,
                ]);
            }
            
            // Actualizar stocks
            foreach ($this->stocks as $sucursal_id => $stock) {
                Stock::updateOrCreate(
                    [
                        'producto_id' => $producto->id,
                        'sucursal_id' => $sucursal_id,
                    ],
                    [
                        'cantidad' => $stock['cantidad'],
                        'stock_minimo' => $stock['stock_minimo'],
                    ]
                );
            }
            
            LivewireAlert::text('Producto actualizado correctamente.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        } else {
            // Crear producto
            $producto = Producto::create([
                'codigo_barra' => $this->codigo_barra,
                'sunat_code' => $this->sunat_code,
                'nombre_producto' => $this->nombre_producto,
                'descripcion' => $this->descripcion,
                'imagen_path' => $this->imagen_path,
                'igv' => $this->igv,
                'precio_base' => $this->precio_base,
                'precio_compra' => $this->precio_compra,
                'categoria_id' => $this->categoria_id,
                'marca_id' => $this->marca_id,
                'negocio_id' => $this->negocio_id,
                'creado_por' => Auth::id(),
                'activo' => $this->activo,
            ]);
            
            // Crear presentaciones
            foreach ($this->presentaciones as $presentacion) {
                $producto->presentaciones()->create([
                    'codigo_barra' => $presentacion['codigo_barra'] ?? null,
                    'unidad_id' => $presentacion['unidad_id'],
                    'descripcion' => $presentacion['descripcion'],
                    'factor' => $presentacion['factor'],
                    'precio' => $presentacion['precio'],
                    'activo' => true,
                ]);
            }
            
            // Crear stocks
            foreach ($this->stocks as $sucursal_id => $stock) {
                Stock::create([
                    'producto_id' => $producto->id,
                    'sucursal_id' => $sucursal_id,
                    'cantidad' => $stock['cantidad'],
                    'stock_minimo' => $stock['stock_minimo'],
                ]);
            }
            
            LivewireAlert::text('Producto creado correctamente.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
        
        $this->closeModal();
    }

    public function delete($uuid)
    {
        $producto = Producto::where('uuid', $uuid)->firstOrFail();
        
        // Verificar permisos
        if (Auth::user()->hasRole('vendedor')) {
            if ($producto->creado_por != Auth::id() || $producto->tieneVentas()) {
                LivewireAlert::text('No tienes permisos para eliminar este producto.')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
                return;
            }
        }
        
        // Eliminar imagen si existe
        if ($producto->imagen_path) {
            Storage::disk('public')->delete($producto->imagen_path);
        }
        
        // Eliminar presentaciones y stocks
        $producto->presentaciones()->delete();
        $producto->stocks()->delete();
        
        $producto->delete();
        LivewireAlert::text('Producto eliminado correctamente.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();
    }

    public function addPresentacion()
    {
        $this->presentaciones[] = [
            'codigo_barra' => '',
            'unidad_id' => '',
            'descripcion' => '',
            'factor' => 1,
            'precio' => 0,
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

