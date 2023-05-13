<?php

namespace Database\Factories;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Survey::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name,
			'from_email' => $this->faker->unique()->safeEmail,
            'redirect_url' => null,
            'description' => $this->faker->paragraph,
            'description_on_email' => $this->faker->paragraph,
            'participants' => $this->faker->randomElement(['1', '0']),
            'is_active' => $this->faker->randomElement(['1', '0']),
        ];
    }
}
