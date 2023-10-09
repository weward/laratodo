<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_successfully()
    {
        $password = 'password123';

        $user = User::factory()->create(['password' => bcrypt($password)]);

        $email = $user->email;

        $res = $this->postJson(route('auth.login'), [
            'email' => $email,
            'password' => $password,
        ]);

        $res->assertStatus(200);
        $res->assertJsonPath('user.email', $email);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('token')->etc()
        );
    }

    public function test_login_invalid_email()
    {
        $res = $this->postJson(route('auth.login'), [
            'email' => '',
            'password' => 'password123',
        ]);

        $res->assertStatus(422);
        $res->assertInvalid(['email']);
    }

    public function test_failed_login_non_existing_email()
    {
        $res = $this->postJson(route('auth.login'), [
            'email' => 'something@else.com',
            'password' => 'password123',
        ]);

        $res->assertStatus(404);
    }
}
