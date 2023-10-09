<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->catchPhrase,
            'description' => $this->faker->text(250),
            // 'attachments' => [],
            'tags' => $this->generateTags()
        ];
    }

    /**
     * Set Task User
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function user($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }

    /**
     * Set task due date
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function dueDate($date = null)
    {
        return $this->state(function (array $attributes) use ($date) {
            return [
                'due_date' => $date ? Carbon::parse($date)->format('Y-m-d') : now()->startOfDay()->format('Y-m-d')
            ];
        });
    }

    /**
     * Set task due date to the future
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function future($days = 3)
    {
        return $this->state(function (array $attributes) use ($days) {

            return [
                'due_date' => now()->startOfDay()->addDays($days)->format('Y-m-d'),
            ];
        });
    }

    /**
     * Set task due date in the past
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function expired($subDays = 3)
    {
        return $this->state(function (array $attributes) use ($subDays) {
            return [
                'due_date' => now()->startOfDay()->subDays($subDays)->format('Y-m-d'),
            ];
        });
    }

    /**
     * Set task completed_at date
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed($daysAgo = 0)
    {
        return $this->state(function (array $attributes) use ($daysAgo) {
            return [
                'completed_at' => now()->startOfDay()->subDays($daysAgo)->format('Y-m-d'),
            ];
        });
    }

    /**
     * Set task completed_at date
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function archived($daysAgo = 0)
    {
        return $this->state(function (array $attributes) use ($daysAgo) {
            return [
                'archived_at' => now()->startOfDay()->subDays($daysAgo)->format('Y-m-d'),
            ];
        });
    }

    /**
     * Set task completed_at date
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function priority($priorityId)
    {
        return $this->state(function (array $attributes) use ($priorityId) {
            return [
                'priority_id' => $priorityId
            ];
        });
    }

    /**
     * Set task completed_at date
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function tags($tagsArr = null)
    {
        return $this->state(function (array $attributes) use ($tagsArr) {
            $tagsArr = (!is_array($tagsArr)) ? [$tagsArr] : $tagsArr;

            return [
                'tags' => $tagsArr
            ];
        });
    }

    protected function generateTags()
    {
        $rand = rand(0, 4);

        if (!$rand) {
            return null;
        }

        $tags = [];
        for ($i = 1; $i <= $rand; $i++) {
            $tags[] = $this->faker->company();
        }

        return cleanTags($tags);
    }

}
