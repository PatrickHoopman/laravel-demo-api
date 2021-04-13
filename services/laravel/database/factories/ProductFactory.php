<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => $this->faker->bothify('#####??'),
            'name' => $this->faker->word,
            'brand' => $this->faker->word,
            'price' =>  $this->faker->randomFloat(2, 20, 30),
            'stock' => $this->faker->randomDigit(),
        ];
    }
}
