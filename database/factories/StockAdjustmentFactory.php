<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\StockAdjustment;

class StockAdjustmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockAdjustment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'quantity_adjusted' => $this->faker->numberBetween(-10000, 10000),
            'reason' => $this->faker->text(),
        ];
    }
}
