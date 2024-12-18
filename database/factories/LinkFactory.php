<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'title' => $this->faker->title(),
            'description' => $this->faker->paragraph(),
            'position' => rand(1, 10),
            'user_id' => User::factory(),
            // 'issue_id' => Issue::factory()
        ];
    }
}
