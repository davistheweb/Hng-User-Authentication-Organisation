<?php

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenTest extends TestCase
{


    /** @test */
    public function it_generates_valid_token()
    {

        $user = User::create([
            'userId' => Str::uuid(),
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password'),
            'phone' => '123456789',
        ]);

        $token = JWTAuth::fromUser($user);

        $this->assertNotNull($token);

    }
}
