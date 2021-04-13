<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\Product;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private function assertProductCollection(AssertableJson $json, $count, $nestedAssertion) {
        $json->has('products', $count, $nestedAssertion);
    }
    
    private function assertProductResource(AssertableJson $json, $nestedAssertion) {
        $json->has('product', $nestedAssertion);
    }
    
    private function assertProduct(AssertableJson $json, Product $product) {
        return $json->where('serial', $product->product_id)
            ->where('name', $product->name)
            ->where('brand', $product->brand)
            ->has('price')
            ->has('stock')
            ->where('outOfStock', $product->out_of_stock)
            ->where('deleted', $product->deleted)
            ->missing('id')
            ->missing('updated_at')
            ->missing('created_at')
            ->missing('deleted_at')
            ->etc();
    }

    private function assertProductValidationErrors(AssertableJson $json) {
        return $json->has('serial')
            ->has('name')
            ->has('brand')
            ->has('price')
            ->etc();
    }

    public function test_all_products_returned_correctly()
    {
        $productCount = 10;
        $products = Product::factory()->count($productCount)->create();
        $product = $products->first();
        $assertProductResource = fn(AssertableJson $json) => 
            $this->assertProduct($json, $product);

        $assertProductCollection = fn(AssertableJson $json) => 
            $this->assertProductCollection($json, 10, $assertProductResource);

        $response = $this->getJson('/api/products');

        $response->assertJson($assertProductCollection);
             
        $response->assertStatus(200);
    }
    
    public function test_product_returned_correctly()
    {
        $productCount = 10;
        $products = Product::factory()->count($productCount)->create();
        $product = $products->first();

        $assertProductResource = fn(AssertableJson $json) => 
            $this->assertProduct($json, $product);

        $assertProductCollection = fn(AssertableJson $json) => 
            $this->assertProductResource($json, $assertProductResource);


        $response = $this->getJson("/api/products/$product->product_id");

        $response->assertJson($assertProductCollection);
             
        $response->assertStatus(200);
    }

    public function test_product_inserted_correctly()
    {
        $product = new Product;
        $product->product_id = 'mocked-serial';
        $product->name = 'mocked-name';
        $product->brand = 'mocked-brand';
        $product->price = 10.00;
        $product->stock = 2;

        $assertProductResource = fn(AssertableJson $json) => 
            $this->assertProduct($json, $product);

        $assertProductCollection = fn(AssertableJson $json) => 
            $this->assertProductResource($json, $assertProductResource);

        $response = $this->postJson('/api/products', [
            "serial" => $product->product_id,
            "name" => $product->name,
            "brand" => $product->brand,
            "price" => $product->price,
            "stock" => $product->stock
        ]);
        
        $response->assertJson($assertProductCollection);
             
        $response->assertStatus(201);
    }

    public function test_product_updates_correctly()
    {
        $productCount = 3;
        $products = Product::factory()->count($productCount)->create();
        $product = $products->first();

        $originalProductId = $product->product_id;

        $product->product_id = 'mocked-serial-changed';
        $product->name = 'mocked-name-changed';
        $product->brand = 'mocked-brand-changed';
        $assertProductResource = fn(AssertableJson $json) => 
            $this->assertProduct($json, $product);

        $assertProductCollection = fn(AssertableJson $json) => 
            $this->assertProductResource($json, $assertProductResource);
        $response = $this->patchJson("/api/products/$originalProductId", [
            "serial" => $product->product_id,
            "name" => $product->name,
            "brand" => $product->brand,
        ]);
        $response->assertJson($assertProductCollection);
             
        $response->assertStatus(200);
    }

    public function test_product_update_restores_deleted_correctly()
    {
        $productCount = 3;
        $products = Product::factory()->count($productCount)->create();
        $product = $products->first();
        $product->delete();

        $response = $this->patchJson("/api/products/$product->product_id", [
            "restore" => true
        ]);

        $product = Product::where('product_id', $product->product_id)
            ->first();

        $assertProductResource = fn(AssertableJson $json) => 
            $this->assertProduct($json, $product);

        $assertProductCollection = fn(AssertableJson $json) => 
            $this->assertProductResource($json, $assertProductResource);

        $response->assertJson($assertProductCollection);

        $response->assertStatus(200);
    }

    public function test_product_deletes_correctly()
    {
        $productCount = 3;
        $products = Product::factory()->count($productCount)->create();
        $product = $products->first();
        
        $response = $this->deleteJson("/api/products/$product->product_id");
        
        $product = Product::onlyTrashed()
            ->where('product_id', $product->product_id)
            ->first();

        $assertProductResource = fn(AssertableJson $json) => 
            $this->assertProduct($json, $product);

        $assertProductCollection = fn(AssertableJson $json) => 
            $this->assertProductResource($json, $assertProductResource);

        $response->assertJson($assertProductCollection);

        $response->assertStatus(200);
    }
    
    public function test_product_insert_throws_validation_exception()
    {
        $product = new Product;
        $product->name = 'mocked-name';
        $product->brand = 'mocked-brand';
        $product->price = 10.00;
        $product->stock = 2;

        $assertValidationErrors = fn(AssertableJson $json) => 
            $this->assertProductValidationErrors($json);

        $assertValidationException = fn(AssertableJson $json) => 
            $json->has('errors', $assertValidationErrors);

        $response = $this->postJson('/api/products', []);
        
        $response->assertJson($assertValidationException);
             
        $response->assertStatus(422);
    }
}
