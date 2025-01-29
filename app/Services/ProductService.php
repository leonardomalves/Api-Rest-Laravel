<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    private int $cacheSeconds;

    public function __construct()
    {
        $this->cacheSeconds = config('cache.ttl', 600);
    }

    /**
     * Exibe uma lista dos recursos de produto.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {

        $fields = [
            'name' => 'like',
            'description' => 'like',
            'price' => 'like',
            'stock' => 'like'
        ];

        $cacheKey = $this->generateCacheKey($request);
        $this->manageProductsCacheKeys($cacheKey);
        return Cache::remember($cacheKey, now()->addSeconds($this->cacheSeconds), function () use ($request, $fields) {
            $products = $this->search($fields, $request);
            return ProductResource::collection($products);
        });

    }

    /**
     * Armazena um novo recurso de produto.
     *
     * @param array $data Dados do produto a ser criado.
     * @return JsonResponse
     */
    public function store(array $data): JsonResponse
    {
        $product = Product::create($data);

        $this->invalidateProductCache();

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    /**
     * Exibe um recurso de produto especificado.
     *
     * @param string $id ID do produto.
     * @return ProductResource|JsonResponse
     */
    public function show(string $id): ProductResource|JsonResponse
    {
        try {

            $product = Product::findOrFail($id);
            return new ProductResource($product);

        } catch (ModelNotFoundException) {

            return response()->json([
                'error' => 'Product not found',
                'message' => 'The product with the specified ID does not exist'
            ], 404);

        }
    }

    /**
     * Atualiza um recurso de produto especificado.
     *
     * @param array $data Dados a serem atualizados no produto.
     * @param string $id ID do produto a ser atualizado.
     * @return ProductResource|JsonResponse
     */
    public function update(array $data, string $id): ProductResource|JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->update($data);

            $this->invalidateProductCache();

            return new ProductResource($product);
        } catch (ModelNotFoundException) {

            return response()->json([
                'error' => 'Product not found',
                'message' => 'The product with the specified ID does not exist'
            ], 404);

        }
    }

    /**
     * Exclui (soft delete) um recurso de produto especificado.
     *
     * @param string $id ID do produto a ser excluído.
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {

            $product = Product::findOrFail($id);
            $product->delete();
            $this->invalidateProductCache();

            return response()->json(['message' => 'Product deleted successfully']);

        } catch (ModelNotFoundException) {

            return response()->json([
                'error' => 'Product not found',
                'message' => 'The product with the specified ID does not exist'
            ], 404);

        }
    }

    /**
     * Realiza uma busca com filtros e paginação.
     *
     * @param array $fields Filtros de campos e operadores.
     * @param Request $request
     * @param string $dateField O campo de data a ser utilizado para filtro (default: 'created_at').
     * @param int $paginate A quantidade de itens por página (default: 25).
     * @return LengthAwarePaginator
     */
    public function search(array $fields, Request $request, string $dateField = 'created_at', int $paginate = 15): LengthAwarePaginator
    {
        $allowedOrderFields = ['id', 'name', 'price', 'stock', 'created_at'];
        $orderField = $request->get('order_field', 'id');
        $orderDirection = $request->get('order_direction', 'asc');

        if (!in_array($orderField, $allowedOrderFields, true)) {
            $orderField = 'id';
        }
        $orderDirection = in_array(strtolower($orderDirection), ['asc', 'desc']) ? $orderDirection : 'asc';

        $query = Product::query()->select(['id', 'name', 'price', 'stock', 'description', 'updated_at', 'created_at']);


        foreach ($request->only(array_keys($fields)) as $field => $value) {
            if ($value !== null) {
                $operator = $fields[$field];
                $query->where($field, $operator === 'like' ? 'LIKE' : $operator, $operator === 'like' ? "%$value%" : $value);
            }
        }

        try {
            if ($request->filled('start')) {
                $startDate = Carbon::parse($request->start)->startOfDay();
                $query->where($dateField, '>=', $startDate);
            }
            if ($request->filled('end')) {
                $endDate = Carbon::parse($request->end)->endOfDay();
                $query->where($dateField, '<=', $endDate);
            }
        } catch (\Exception $e) {
            Log::error('Invalid date format in search filter', ['error' => $e->getMessage()]);
        }

        return $query->orderBy($orderField, $orderDirection)->paginate($paginate);
    }

    private function generateCacheKey(Request $request): string
    {
        $queryParams = $request->query();
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $cacheVersion = Cache::get('products_cache_version', 1);

        return "products_v{$cacheVersion}_" . md5($queryString);
    }

    public function manageProductsCacheKeys(string $cacheKey): void
    {
        $keys = Cache::get('product_cache_keys', []);
        $keys[] = $cacheKey;
        Cache::put('product_cache_keys', array_unique($keys), now()->addHours(1));
    }

    private function invalidateProductCache(): void
    {
        $keys = Cache::get('product_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget('product_cache_keys');
    }
}
