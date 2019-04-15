<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    /**
    *@test
    */
    public function new_user_can_register_with_valid_credentials()
    {
        $response = $this->json('POST', '/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@d.com',
            'password' => 'secret', // secret,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users',
            [
                'name'       => 'John Doe',
                'email'      => 'john@d.com',
                'api_token'  => null,
            ]
        );

        //$this->assertTrue(Hash::check('secret', $user->password));
    }

    /**
    *@test
    */
    public function new_user_can_not_register_if_name_email_or_password_are_not_providede()
    {
        $response = $this->json('POST', '/api/auth/register', [
            'name' => 'John Doe',
            'password' => 'secret', // secret,
        ]);
        $response->assertStatus(422);

        $response2 = $this->json('POST', '/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@d.com', // secret,
        ]);
        $response2->assertStatus(422);

        $response3 = $this->json('POST', '/api/auth/register', [
            'password' => 'secret',
            'email' => 'john@d.com', // secret,
        ]);
        $response3->assertStatus(422);
    }

    /**
    *@test
    */
    public function new_user_cannot_register_if_password_is_less_than_six_character()
    {
        $response = $this->json('POST', '/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@d.com',
            'password' => 'short',
        ]);

        $response->assertStatus(422);
    }

    /**
    *@test
    */
    public function new_user_can_not_register_if_email_is_in_invalid_format()
    {
        $response = $this->json('POST', '/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'invalid-format',
            'password' => 'secret',
        ]);

        $response->assertStatus(422);
    }

    /**
    *@test
    */
    public function new_user_can_not_register_if_email_is_not_unique()
    {
        factory('App\User')->create(['email'=>'john@d.com']);

        $response = $this->json('POST', '/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@d.com',
            'password' => 'short',
        ]);
        $response->assertStatus(422);
    }

    /**
    *@test
    */
    public function registered_user_can_login_with_valid_params()
    {
        $this->withoutExceptionHandling();
        $user = factory('App\User')->create(['password' => bcrypt($password = 'secret')]);

        $response = $this->json('POST', '/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                 ]);;
        //$this->assertTrue(Hash::check('secret', $user->password));
        $this->assertNotNull($user->fresh()->api_token);

    }

    public function user_can_not_login_if_email_does_not_exist()
    {
        factory('App\User')->create(['email' => 'john@doe.com']);

        $response = $this->json('POST', '/api/auth/login', [
            'email' => 'otheremail@e.com',
            'password' => 'secret',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status'=>'error',
                'message' => 'User not found'
            ]);;
    }

    /**
    *@test
    */
    public function user_can_not_login_if_password_is_incorrect()
    {
        $user = factory('App\User')->create(['password' => bcrypt($password = 'secret')]);

        $response = $this->json('POST', '/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status'=>'error',
                'message' => 'Invalid Credentials'
            ]);;
    }

    /**
    *@test
    */
    public function logged_user_can_logout_with_valid_route()
    {
        $user = factory('App\User')->create(['api_token' => 'valid_token']);
        $response = $this->json('POST', '/api/auth/logout',
            ['api_token' => 'valid_token']);
        $response->assertStatus(200)
            ->assertJson([
                'status'=>'Success',
                'message' => 'You successfully logged out'
            ]);
        $this->assertNull($user->fresh()->api_token);
    }

    /**
    *@test
    */
    public function user_can_not_logout_if_api_token_is_not_provided_or_does_not_match_token_from_database()
    {
        $user = factory('App\User')->create(['api_token' => 'valid_token']);

        $response = $this->json('POST', '/api/auth/logout',[
            'some_wrong_request' => 'error']);
        $response->assertStatus(422);
        $this->assertNotNull($user->fresh()->api_token);

        $response2 = $this->json('POST', '/api/auth/logout',
            ['api_token' => 'wrong_token']);
        $response2->assertStatus(404);
        $this->assertNotNull($user->fresh()->api_token);
    }
}
