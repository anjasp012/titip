<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Provider::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'provider_url_order' => $this->faker->name,
            'provider_url_status' => $this->faker->name,
            'provider_url_service' => $this->faker->name,
            'provider_id' => $this->faker->name,
            'provider_key' => $this->faker->name,
            'provider_secret' => $this->faker->name,
        ];
    }
}
