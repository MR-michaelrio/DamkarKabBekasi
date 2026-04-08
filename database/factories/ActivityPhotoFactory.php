<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'activity_log_id' => ActivityLog::factory(),
            'photo_path' => 'activity-photos/' . fake()->numberBetween(1, 100) . '/' . uniqid() . '.jpg',
            'photo_name' => fake()->sentence() . '.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(500000, 5000000), // 500KB to 5MB
            'description' => fake()->sentence(),
            'sequence' => fake()->numberBetween(1, 5),
        ];
    }

    /**
     * Set a specific activity log
     */
    public function forActivity(ActivityLog $activity): self
    {
        return $this->state(fn (array $attributes) => [
            'activity_log_id' => $activity->id,
        ]);
    }

    /**
     * Set sequence order
     */
    public function withSequence(int $sequence): self
    {
        return $this->state(fn (array $attributes) => [
            'sequence' => $sequence,
        ]);
    }
}
