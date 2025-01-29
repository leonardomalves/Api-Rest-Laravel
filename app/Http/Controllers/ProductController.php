<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Serviço responsável pelas operações de produtos.
     *
     * @var ProductService
     */
    protected ProductService $productService;

    /**
     * Construtor do controlador, injetando o serviço de produtos.
     *
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Exibe uma listagem de produtos.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {

        return $this->productService->index($request);
    }

    /**
     * Armazena um novo produto.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['string', 'nullable'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'integer'],
        ];

        try {

            $request->validate($rules);
            $filteredData = $request->only(array_keys($rules));

            return $this->productService->store($filteredData);

        } catch (ValidationException $e) {

            return response()->json([
                'erro' => 'validation_error',
                'message' => 'There was a validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Exibe o produto especificado.
     *
     * @param string $id
     * @return ProductResource|JsonResponse
     */
    public function show(string $id)
    {
        return $this->productService->show($id);
    }

    /**
     * Atualiza o produto especificado.
     *
     * @param Request $request
     * @param string $id
     * @return ProductResource|JsonResponse
     */
    public function update(Request $request, string $id)
    {

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['string', 'nullable'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'integer'],
        ];

        try {

            $request->validate($rules);
            $filteredData = $request->only(array_keys($rules));

            return $this->productService->update($filteredData, $id);

        } catch (ValidationException $e) {

            return response()->json([
                'erro' => 'validation_error',
                'message' => 'There was a validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Remove o produto especificado.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        return $this->productService->destroy($id);
    }
}
