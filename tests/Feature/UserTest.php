<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


/**
 * @property  user
 */
class UserTest extends TestCase
{
    use RefreshDatabase;


    public function setUp() :void
    {
        parent::setUp();

        $this->user = factory(User::class)->make();
    }

    /** @test */
    function name_should_not_be_too_long()
    {
        $response = $this->post('api/users', [
            'name' => str_repeat('a', 51),
            'email' => $this->user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'The name may not be greater than 50 characters.'
        ]);
    }
    /** @test */
    function name_is_just_long_enough_to_pass()
    {
        $response = $this->post('api/users', [
            'name' => str_repeat('a', 50),
            'email' => $this->user->email,
            'password' => 'secret',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $this->user->email,
        ]);
        $response->assertStatus(201);
    }

    /** @test */
    function email_should_not_be_too_long()
    {
        $response = $this->post('api/users', [
            'name' => $this->user->name,
            'email' => str_repeat('a', 247).'@test.com', // 256
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email may not be greater than 255 characters.'
        ]);
    }

    /** @test */
    function email_validation_should_reject_invalid_emails()
    {
        collect(['you@example,com', 'bad_user.org', 'example@bad+user.com'])->each(function ($invalidEmail) {
            $this->post('api/users', [
                'name' => $this->user->name,
                'email' => $invalidEmail,
                'password' => 'secret',
            ])->assertSessionHasErrors([
                'email' => 'The email must be a valid email address.'
            ]);
        });
    }


}
