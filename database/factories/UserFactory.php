<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'username' => fake()->name(),
            'parent_id' => null,
            'user_type' => 'private',
            'email' => fake()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that user is parent.
     *
     * @return static
     */
    public function parentUser()
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }

    /**
     * Indicate that user is child.
     *
     * @return static
     */
    public function childUser($parentId)
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Indicate that user_type is business.
     *
     * @return static
     */
    public function businessUser()
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'business',
        ]);
    }
    /**
     * Indicate that user_type is private.
     *
     * @return static
     */
    public function privateUser()
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'private',
        ]);
    }
    /**
     * Indicate that user email is unverified.
     *
     * @return static
     */
      public function unverifiedUser()
      {
          return $this->state(fn (array $attributes) => [
                'email_verified_at' => null,
          ]);
      }
      public function overridePassword($password)
      {
          return $this->state(fn (array $attributes) => [
                'password' => $password,
          ]);
      }
}
