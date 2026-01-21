<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Listar todos los productos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::query();

            // Búsqueda
            if ($request->has("search")) {
                $search = $request->input("search");
                $query->where(function ($q) use ($search) {
                    $q->where("name", "like", "%{$search}%")->orWhere(
                        "description",
                        "like",
                        "%{$search}%",
                    );
                });
            }

            // Ordenamiento
            $sortBy = $request->input("sort_by", "created_at");
            $sortDirection = $request->input("sort_direction", "desc");
            $query->orderBy($sortBy, $sortDirection);

            // Paginación
            $perPage = $request->input("per_page", 15);
            $perPage = min($perPage, 100); // Máximo 100 por página

            $products = $query->paginate($perPage);

            // Formatear respuesta
            $data = $products->map(function ($product) {
                return [
                    "id" => $product->id,
                    "name" => $product->name,
                    "description" => $product->description,
                    "price" => (float) $product->price,
                    "image" => $product->image
                        ? asset("storage/" . $product->image)
                        : null,
                    "has_image" => $product->hasImage(),
                    "created_at" => $product->created_at,
                    "updated_at" => $product->updated_at,
                ];
            });

            return response()->json(
                [
                    "success" => true,
                    "data" => $data,
                    "pagination" => [
                        "current_page" => $products->currentPage(),
                        "last_page" => $products->lastPage(),
                        "per_page" => $products->perPage(),
                        "total" => $products->total(),
                        "from" => $products->firstItem(),
                        "to" => $products->lastItem(),
                    ],
                    "meta" => [
                        "search" => $request->input("search"),
                        "sort_by" => $sortBy,
                        "sort_direction" => $sortDirection,
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al obtener productos",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Crear un nuevo producto
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                "name" => "required|string|max:100",
                "description" => "required|string|max:1000",
                "price" => "required|numeric|min:0.01|max:9999999",
                "image" =>
                    "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            ]);

            // Crear producto
            $product = Product::create([
                "name" => $validated["name"],
                "description" => $validated["description"],
                "price" => $validated["price"],
            ]);

            // Procesar imagen si se subió
            if ($request->hasFile("image")) {
                $uploadResult = $this->fileService->upload(
                    $request->file("image"),
                    "products",
                );

                if ($uploadResult["success"]) {
                    $product->image = $uploadResult["path"];
                    $product->save();
                }
            }

            return response()->json(
                [
                    "success" => true,
                    "message" => "Producto creado exitosamente",
                    "data" => [
                        "product" => [
                            "id" => $product->id,
                            "name" => $product->name,
                            "description" => $product->description,
                            "price" => (float) $product->price,
                            "image" => $product->image
                                ? asset("storage/" . $product->image)
                                : null,
                            "has_image" => $product->hasImage(),
                            "created_at" => $product->created_at,
                            "updated_at" => $product->updated_at,
                        ],
                    ],
                ],
                201,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error de validación",
                    "errors" => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al crear producto",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Mostrar un producto específico
     */
    public function show(Product $product): JsonResponse
    {
        try {
            return response()->json(
                [
                    "success" => true,
                    "data" => [
                        "product" => [
                            "id" => $product->id,
                            "name" => $product->name,
                            "description" => $product->description,
                            "price" => (float) $product->price,
                            "image" => $product->image
                                ? asset("storage/" . $product->image)
                                : null,
                            "has_image" => $product->hasImage(),
                            "created_at" => $product->created_at,
                            "updated_at" => $product->updated_at,
                        ],
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al obtener producto",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Actualizar un producto
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        try {
            $validated = $request->validate([
                "name" => "required|string|max:40",
                "price" => "required|numeric|min:.01|max:9999999.99",
                "description" => "required|string",
                "image" =>
                    "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
                "remove_image" => "sometimes|boolean",
            ]);

            // Actualizar campos básicos
            $product->fill(
                // $validated
                array_intersect_key(
                    $validated,
                    array_flip(["name", "description", "price"]),
                ),
            );
            $product->save();
            // $product->update($validated);
            // dd($validated, $product);
            // Manejar imagen
            if ($request->input("remove_image", false)) {
                // Eliminar imagen actual
                if ($product->image) {
                    $this->fileService->delete($product->image);
                    $product->image = null;
                    $product->save();
                }
            } elseif ($request->hasFile("image")) {
                // Subir nueva imagen
                $oldImage = $product->image;

                $uploadResult = $this->fileService->upload(
                    $request->file("image"),
                    "products",
                );

                if ($uploadResult["success"]) {
                    $product->image = $uploadResult["path"];
                    $product->save();

                    // Eliminar imagen anterior
                    if ($oldImage) {
                        $this->fileService->delete($oldImage);
                    }
                }
            }

            return response()->json(
                [
                    "success" => true,
                    "message" => "Producto actualizado exitosamente",
                    "data" => [
                        "product" => [
                            "id" => $product->id,
                            "name" => $product->name,
                            "description" => $product->description,
                            "price" => (float) $product->price,
                            "image" => $product->image
                                ? asset("storage/" . $product->image)
                                : null,
                            "has_image" => $product->hasImage(),
                            "created_at" => $product->created_at,
                            "updated_at" => $product->updated_at,
                        ],
                    ],
                ],
                200,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error de validación",
                    "errors" => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al actualizar producto",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Eliminar un producto
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            // Eliminar imagen si existe
            if ($product->image) {
                $this->fileService->delete($product->image);
            }

            $product->delete();

            return response()->json(
                [
                    "success" => true,
                    "message" => "Producto eliminado exitosamente",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al eliminar producto",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Subir imagen para un producto (endpoint específico)
     */
    public function uploadImage(
        Request $request,
        Product $product,
    ): JsonResponse {
        try {
            $validated = $request->validate([
                "image" =>
                    "required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120",
            ]);

            $oldImage = $product->image;

            $uploadResult = $this->fileService->upload(
                $request->file("image"),
                "products",
            );

            if (!$uploadResult["success"]) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Error al subir imagen",
                        "error" => $uploadResult["message"],
                    ],
                    400,
                );
            }

            $product->image = $uploadResult["path"];
            $product->save();

            // Eliminar imagen anterior
            if ($oldImage) {
                $this->fileService->delete($oldImage);
            }

            return response()->json(
                [
                    "success" => true,
                    "message" => "Imagen subida exitosamente",
                    "data" => [
                        "image_url" => asset("storage/" . $product->image),
                        "image_path" => $product->image,
                    ],
                ],
                200,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error de validación",
                    "errors" => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al subir imagen",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Eliminar imagen de un producto
     */
    public function deleteImage(Product $product): JsonResponse
    {
        try {
            if (!$product->image) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "El producto no tiene imagen",
                    ],
                    400,
                );
            }

            $this->fileService->delete($product->image);
            $product->image = null;
            $product->save();

            return response()->json(
                [
                    "success" => true,
                    "message" => "Imagen eliminada exitosamente",
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al eliminar imagen",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Obtener estadísticas de productos
     */
    public function stats(): JsonResponse
    {
        try {
            $total = Product::count();
            $withImages = Product::whereNotNull("image")->count();
            $withoutImages = $total - $withImages;
            $avgPrice = Product::avg("price");
            $maxPrice = Product::max("price");
            $minPrice = Product::min("price");

            return response()->json(
                [
                    "success" => true,
                    "data" => [
                        "total_products" => $total,
                        "products_with_images" => $withImages,
                        "products_without_images" => $withoutImages,
                        "average_price" => round((float) $avgPrice, 2),
                        "max_price" => (float) $maxPrice,
                        "min_price" => (float) $minPrice,
                        "percentage_with_images" =>
                            $total > 0
                                ? round(($withImages / $total) * 100, 2)
                                : 0,
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error al obtener estadísticas",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
