<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    private $createProductRules = [
        'serial' => 'string|max:255|required',
        'name' => 'string|max:255|required',
        'brand' => 'string|max:255|required',
        'price' => 'numeric|required',
        'stock' => 'numeric',
    ];

    private $updateProductRules = [
        'serial' => 'string|max:255',
        'name' => 'string|max:255',
        'brand' => 'string|max:255',
        'price' => 'numeric',
        'stock' => 'numeric',
        'restore' => 'boolean',
    ];
    public function index()
    {
        return new ProductCollection(Product::all());
    }

    public function store(Request $request)
    {
        $isValid = $this->validateProductInput($request, $this->createProductRules);

        if ($isValid) {
            $product = Product::withTrashed()->firstOrCreate(
                ['product_id' => $request->input('serial')],
                [
                    'name' => $request->input('name'), 
                    'brand' => $request->input('brand'),
                    'price' => $request->input('price'),
                    'stock' => $request->input('stock', 0)
                ]
            );
            return new ProductResource($product);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($serial)
    {
        return new ProductResource(Product::withTrashed()->where('product_id', '=', $serial)->firstOrFail());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $serial)
    {
        $isValid = $this->validateProductInput($request, $this->updateProductRules);

        if ($isValid) {
            $product = Product::withTrashed()->where('product_id', '=', $serial)->firstOrFail();
            $product->product_id = $request->input('serial', $product->getOriginal('product_id'));
            $product->name = $request->input('name', $product->getOriginal('name'));
            $product->brand = $request->input('brand', $product->getOriginal('brand'));
            $product->price = $request->input('price', $product->getOriginal('price'));
            $product->stock = $request->input('stock', $product->getOriginal('stock'));
            if($request->input('restore', false) && $product->trashed()) {
                $product->restore();
            } else {
                $product->save();
            }
            return new ProductResource($product);
        }
    }

    private function validateProductInput(Request $request, $rules) 
    {
        $validator = Validator($request->all(), $rules);

        if($validator->fails()) {
            $this->failedValidation($validator);
        } else {
            return true;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($serial)
    {
        Product::where('product_id', $serial)->delete();
        return $this->show($serial);
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
