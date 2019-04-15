<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    /**
    *@test
    */
    public function user_can_be_found_by_email_address()
    {
        $user1 = factory('App\User')->create(['email' => 'john@doe.com']);
        $user2 = factory('App\User')->create(['email' => 'john@smith.com']);

        $this->assertEquals($user1::foundBy('email',$user1->email),$user1->fresh());
        $this->assertNotEquals($user1::foundBy('email',$user1->email),$user2->fresh());
    }

    /**
    *@test
    */
    public function user_can_generete_api_token()
    {
        $user = factory('App\User')->create(['api_token' => null]);
        $this->assertNull($user->fresh()->api_token);

        $user->generateApiToken();
        $this->assertNotNull($user->fresh()->api_token);
    }

    /**
    *@test
    */
    public function user_can_delete_api_token()
    {
        $user = factory('App\User')->create(['api_token' => 'some-valid-api-token']);
        $this->assertNotNull($user->fresh()->api_token);

        $user->deleteApiToken();
        $this->assertNull($user->fresh()->api_token);
    }
}
