<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_user_successfully_with_default_organisation()
    {
        $userData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'accessToken',
                    'user' => [
                        'userId',
                        'firstName',
                        'lastName',
                        'email',
                        'phone',
                    ],
                ],
            ]);


        $this->assertDatabaseHas('organisations', [
            'name' => "John's Organisation",
        ]);
    }

    /** @test */
    public function it_logs_in_user_successfully()
    {
        $password = 'password';

        $user = User::create([
            'userId' => Str::uuid(),
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password'),
            'phone' => '123456789',
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->postJson('api/auth/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'accessToken',
                    'user' => [
                        'userId',
                        'firstName',
                        'lastName',
                        'email',
                        'phone',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_fails_to_register_user_with_missing_fields()
    {
        $response = $this->postJson('api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [

                ],
            ]);
    }

    /** @test */
    public function it_fails_to_register_user_with_duplicate_email()
    {
        $user = User::create([
            'userId' => Str::uuid(),
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password'),
            'phone' => '123456789',
        ]);

        $userData = [
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Registration unsuccessful',
            ]);
    }
}
