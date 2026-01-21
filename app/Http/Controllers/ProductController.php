<?php

namespace App\Http\Controllers;

// use App\Http\Requests\ProductStoreRequest;
// use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Services\FileService;
// use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
// use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function index(Request $request)
    {
        // $products = Product::all();

        return view("products.index", [
            // 'products' => $products,
            "products" => collect(),
        ]);
    }

    public function create(Request $request)
    {
        return view("products.form");
    }

    public function store(Request $request)
    {
        try {
            // Debug: Verificar si hay archivo
            Log::info("Store method called");
            Log::info("All request data:", $request->all());
            Log::info("All files:", $request->allFiles());
            Log::info(
                "Has file image: " .
                    ($request->hasFile("image") ? "YES" : "NO"),
            );
            if ($request->hasFile("image")) {
                $file = $request->file("image");
                Log::info(
                    "File info: " .
                        json_encode([
                            "name" => $file->getClientOriginalName(),
                            "size" => $file->getSize(),
                            "mime" => $file->getMimeType(),
                            "valid" => $file->isValid(),
                            "error" => $file->getError(),
                        ]),
                );
            }

            // validar los inputs del request (sin validación de imagen por ahora)
            $validated = $request->validate([
                "name" => "required|string|max:40",
                "price" => "required|numeric|min:1|max:9999999",
                "description" => "required|string",
            ]);

            // No agregar imagen al validated array ya que no está validada
            // La procesaremos por separado

            $id = $request->input("id", null);

            if ($id) {
                // actualizar producto existente
                $product = Product::findOrFail($id);
                $oldImage = $product->image;

                // Actualizar campos básicos
                $product->fill($validated);
                $product->save();

                // Procesar nueva imagen si se subió
                if ($request->hasFile("image")) {
                    $uploadResult = $this->fileService->upload(
                        $request->file("image"),
                        "products",
                    );

                    if ($uploadResult["success"]) {
                        $product->image = $uploadResult["path"];
                        $product->save();

                        // Eliminar imagen anterior si existe
                        if ($oldImage) {
                            $this->fileService->delete($oldImage);
                        }
                    }
                }
            } else {
                // agregar producto nuevo (sin imagen primero)
                $product = Product::create($validated);

                // Procesar imagen para producto nuevo
                if ($request->hasFile("image")) {
                    Log::info("Processing new product image");
                    $uploadResult = $this->fileService->upload(
                        $request->file("image"),
                        "products",
                    );

                    Log::info("Upload result: " . json_encode($uploadResult));

                    if ($uploadResult["success"]) {
                        $product->image = $uploadResult["path"];
                        $product->save();
                        Log::info(
                            "Image path saved: " . $uploadResult["path"],
                        );
                    } else {
                        Log::error(
                            "Image upload failed: " . $uploadResult["message"],
                        );
                    }
                }
            }
            return redirect()
                ->route("products.index")
                ->with("success", "Producto registrado exitosamente.");
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with("error", $e->getMessage());
        }
    }

    public function edit(Request $request, Product $product)
    {
        //$product = Product::find($product);

        return view("products.form", [
            "product" => $product,
        ]);
    }

    // public function update(ProductUpdateRequest $request, Product $product)
    // {
    //     $product->update($request->validated());

    //     session()->flash('Product.name', $product->name);

    //     return redirect()->route('products.index');
    // }

    public function destroy(Request $request, Product $product)
    {
        // Eliminar imagen si existe
        if ($product->image) {
            $this->fileService->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route("products.index")
            ->with("success", "Producto eliminado exitosamente!!!");
    }

    public function dataTable(Request $request)
    {
        // Validar params de DataTables (opcional, pero seguro)
        $request->validate([
            "draw" => "integer",
            "start" => "integer|min:0",
            "length" => "integer|min:1|max:100",
            "search.value" => "nullable|string|max:255",
        ]);

        // Query base
        $query = Product::query();

        // Búsqueda en varios campos
        $search = $request->input("search.value");
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where("name", "like", "%{$search}%")
                    ->orWhere("description", "like", "%{$search}%")
                    ->orWhere("price", "like", "%{$search}%");
            });
        }

        // Total de registros sin filtros (para recordsTotal)
        $totalRecords = Product::count();

        // Registros filtrados (recordsFiltered)
        $filteredRecords = clone $query;
        $recordsFiltered = $filteredRecords->count();

        // get y set Ordenación (columna y dirección)
        $columns = ["name", "description", "price", "id"]; // Orden de columnas en tabla
        $orderColumn = $request->input("order.0.column", 0);
        $orderDir = $request->input("order.0.dir", "asc");
        $query->orderBy($columns[$orderColumn] ?? "id", $orderDir);

        // Paginación
        $start = $request->input("start", 0);
        $length = $request->input("length", 10);
        $data = $query->skip($start)->take($length)->get();

        // Formatear los datos para el componente DataTables
        // TODO: Formatear del lado del cliente
        $data = $data->map(function ($product) {
            $imageHtml = "";
            if (
                $product->image &&
                Storage::disk("public")->exists($product->image)
            ) {
                $imageHtml =
                    '<img src="' .
                    asset("storage/" . $product->image) .
                    '" alt="' .
                    $product->name .
                    '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">';
            } else {
                $imageHtml =
                    '<div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;"><i class="bi bi-image text-muted"></i></div>';
            }

            return [
                "image" => $imageHtml,
                "name" => $product->name,
                "description" => $product->description,
                "price" => '$' . number_format($product->price, 2),
                "actions" =>
                    '
                    <button class="btn btn-primary btn-sm" onclick="execute(\'/products/' .
                    $product->id .
                    '/edit\')">
                        <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteRecord(\'/products/' .
                    $product->id .
                    '\')">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                    </button>
                ',
            ];
        });

        // Respuesta JSON en formato requerido por DataTables
        return response()->json([
            "draw" => (int) $request->input("draw"), // Eco del draw para sync
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ]);
    }

    /**
     * Descargar imagen de producto
     */
    public function downloadImage(Product $product)
    {
        if (!$product->image) {
            abort(404, "Imagen no encontrada");
        }

        try {
            return $this->fileService->download(
                $product->image,
                "producto_" . $product->id . "_" . basename($product->image),
            );
        } catch (\Exception $e) {
            abort(404, "Archivo no encontrado");
        }
    }
}
