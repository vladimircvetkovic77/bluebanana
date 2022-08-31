<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $userOne = User::factory()->parentUser()->create();
        $userTwo = User::factory()->childUser($userOne->id)->create();
        $this->assertEquals($userOne->id, $userTwo->parent_id);
        //   get all users
        $parentUsers = User::parents()->get();

        $this->assertEquals(1, $parentUsers->count());

        //   all users
        $allUsers = User::all();
        $this->assertEquals(2, $allUsers->count());
    }
}
