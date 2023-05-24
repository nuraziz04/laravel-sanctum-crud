<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\TransactionResource;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $transactions = DB::table('transactions')
            ->join('products', 'products.id', '=', 'transactions.product_id')
            ->select('transactions.*', 'products.name as product_name')
            ->get();

        return $this->sendResponse(TransactionResource::collection($transactions), 'Transactions retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'product_id' => ['required'],
            'customer_name' => ['required'],
            'qty' => ['required','numeric']
        ]);

        if($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = Product::find($input['product_id']);

        if(is_null($product)) {
            return $this->sendError('Product not found', null, Response::HTTP_NOT_FOUND);
        }

        $transaction = Transaction::create($input);

        $transaction['product_name'] = $product->name;

        return $this->sendResponse(new TransactionResource($transaction), 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::find($id);

        if(is_null($transaction)) {
            return $this->sendError('Transaction not found', null, Response::HTTP_NOT_FOUND);
        }

        $product = Product::find($transaction->product_id);

        $transaction['product_name'] = $product->name;

        return $this->sendResponse(new TransactionResource($transaction), 'Transactions retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'product_id' => ['required','numeric'],
            'customer_name' => ['required'],
            'qty' => ['required','numeric']
        ]);

        if($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $transaction = Transaction::find($id);
        $product = Product::find($input['product_id']);

        if(is_null($transaction) || is_null($product)) {
            return $this->sendError('Transaction or Product not found', null, Response::HTTP_NOT_FOUND);
        }

        $transaction->update($input);
        $transaction['product_name'] = $product->name;

        return $this->sendResponse(new TransactionResource($transaction), 'Transactions updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::find($id);

        if(is_null($transaction)) {
            return $this->sendError('Transaction not found', null, Response::HTTP_NOT_FOUND);
        }

        $transaction->delete($id);

        return $this->sendResponse([], 'Transaction deleted successfully.');
    }
}
