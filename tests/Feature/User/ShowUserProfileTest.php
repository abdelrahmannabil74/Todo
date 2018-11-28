<?php

namespace Tests\Feature\User;

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
        $user_factory=factory(User::class)->create();

        $response = $this->actingAs($user_factory)->hitShowProfileEndpoint($user_factory);

        $response->assertStatus(200);

    }


}
