<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private array $userData = [
      'username' => 'John Doe',
      'email' => 'vladimir@me.com',
      'password' => 'secret01',
      'password_confirmation' => 'secret01',
      'user_type' => 'private',
];
    public function test_that_username_is_required_field_when_creating_user()
    {
        $this->userData['username'] = '';
        $this->json('POST', 'api/register', $this->userData)
        ->assertStatus(config('responses.UNPROCESSABLE_ENTITY.code'));
    }
    public function test_that_password_is_required_field_when_creating_user()
    {
        $this->userData['password'] = '';
        $this->json('POST', 'api/register', $this->userData)
        ->assertStatus(config('responses.UNPROCESSABLE_ENTITY.code'));
    }
    public function test_that_email_is_required_field_when_creating_user()
    {
        $this->userData['email'] = '';
        $this->json('POST', 'api/register', $this->userData)
        ->assertStatus(config('responses.UNPROCESSABLE_ENTITY.code'));
    }
    public function test_that_email_field_is_in_email_format_when_creating_user()
    {
        $this->userData['email'] = 'not_email';
        $this->json('POST', 'api/register', $this->userData)
        ->assertStatus(config('responses.UNPROCESSABLE_ENTITY.code'));
    }
    public function test_that_user_can_be_created()
    {
        $response = $this->post('/api/register', $this->userData);
        // change the response from json to array
        $responseArray = json_decode($response->getContent(), true);
        $response->assertStatus(config('responses.CREATED.code'));
        $this->assertEquals($this->userData['username'], $responseArray['data']['username']);
        $this->assertEquals($this->userData['email'], $responseArray['data']['email']);
        $this->assertEquals($this->userData['user_type'], $responseArray['data']['user_type']);
    }

    public function test_that_email_is_not_verified_when_user_is_created()
    {
        $response = $this->post('/api/register', $this->userData);
        $responseArray = json_decode($response->getContent(), true);
        $this->assertFalse($responseArray['data']['email_verified']);
    }
    public function test_that_token_is_issued_when_user_is_created()
    {
        $response = $this->post('/api/register', $this->userData);
        $responseArray = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $responseArray['data']);
    }

    public function test_that_user_can_not_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret01'),
        ]);
        $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
        ])
        ->assertStatus(config('responses.BAD_REQUEST.code'));
        $this->assertGuest();
    }

    public function test_that_user_can_log_in()
    {
        $user = User::factory()->create([
              'password' => bcrypt($password = 'i-love-laravel'),
          ]);

        $response = $this->post('api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);
    }
    public function test_that_token_is_created_in_password_resets_table_wnen_user_forgots_password()
    {
        $user = User::factory()->create();
        $response = $this->post('api/forgot-password', [
              'email' => $user->email,
        ]);
        //  get reset token from email
        $token = DB::table('password_resets')->first();
        $this->assertNotNull($token);
    }
}
