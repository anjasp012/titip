<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => trim($this->faker->userName),
            'password' => Hash::make('password'),
            'full_name' => $this->faker->name,
            'email' => 'email@domain.com',
            'phone_number' => '085349680016',
            'status' => '1',
            'level' => $this->faker->randomElement(['Member', 'Agen', 'Reseller', 'Admin']),
        ];
    }
}
