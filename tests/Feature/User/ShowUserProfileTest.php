<?php

namespace Tests\Feature\User;

use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowUserProfileTest extends TestCase
{


    private function hitShowProfileEndpoint($user): TestResponse
    {
        $response = $this->get(route('profile', ['user' => $user->id]));
        return $response;
    }


    function test_authenticated_user_can_view_his_profile()
    {
        $user=factory(User::class)->create();

        $this->actingAs($user);

        $response = $this->hitShowProfileEndpoint($user);

        $response->assertStatus(200);

        $user->all()->last();

        $response->assertJson(\Fractal::item($user, new UserTransformer())->toArray());

    }


    function test_guest_cant_view_his_profile()
    {
        $user=factory(User::class)->create();

        $response = $this->hitShowProfileEndpoint($user);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);


    }

}
