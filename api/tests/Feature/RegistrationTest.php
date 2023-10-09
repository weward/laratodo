<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_registration_successful()
    {
        $email = 'test@test.com';
        $password = "password";

        $res = $this->postJson(route('auth.register', [
            'email' => $email,
            'name' => 'Test User',
            'password' => $password,
            'password_confirmation' => $password,
        ]));

        $res->assertStatus(200);
        $res->assertJsonPath('user.email', $email);
        $res->assertJson(fn (AssertableJson $json) =>
            $json->has('token')->etc()
        );
    }

    public function test_registration_email_already_taken()
    {
        $user = User::factory()->create(['email' => 'user@mail.com']);

        $password = "password";

        $res = $this->postJson(route('auth.register', [
            'email' => $user->email,
            'name' => 'Test User 2',
            'password' => $password,
            'password_confirmation' => $password,
        ]));

        $res->assertStatus(422);
        $res->assertInvalid(['email']);
    }
}
