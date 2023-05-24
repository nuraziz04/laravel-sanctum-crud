<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseController
{
    public function index(): JsonResponse
    {
        $products = Product::latest()->get();

        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = Product::create($input);

        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::find($id);

        if(is_null($product)) {
            return $this->sendError('Product not found', null, Response::HTTP_NOT_FOUND);
        }

        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => ['required'],
            'detail' => ['required']
        ]);

        if($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = Product::find($id);

        if(is_null($product)) {
            return $this->sendError('Product not found', null, Response::HTTP_NOT_FOUND);
        }

        $product->update($input);

        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);

        if(is_null($product)) {
            return $this->sendError('Product not found', null, Response::HTTP_NOT_FOUND);
        }

        $product->delete($id);

        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
