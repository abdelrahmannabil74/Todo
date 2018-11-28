<?php

namespace Tests\Feature\User;

use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserProfileTest extends TestCase
{

    private function hitUpdateProfileEndpoint($user, $userData): TestResponse
    {
        $response = $this->put(route('updateProfile', ['user' => $user->id]), $userData);
        return $response;
    }


    /**
     * A data provider for the non-required fields to update a user in updateProfile endpoint.
     *
     * @return array
     */
    public function nonRequiredFields()
    {
        return [

            ['title'],
            ['is_complete'],
        ];
    }

    /**
     * A data provider for the required fields to update a user in updateProfile endpoint.
     *
     * @return array
     */
    public function requiredFields()
    {
        return [

            ['is_private'],
            ['deadline'],];
    }

    /**
     * A data provider for the invalid values of fields to update a user in updateProfile endpoint.
     *
     * @return array
     */
    public function invalidFieldsValues()
    {
        return [

            ['is_private',22],
            ['is_private','string'],

            ['deadline',22],
            ['deadline','string'],
            ['deadline',false],
        ];
    }


    /**
     *
     * @test
     */
    function test_authenticated_user_can_update_his_profile_data()
    {
        $user=factory(User::class)->create();

        $userData = [
            'info'=>$user->info,
            'avatar'=>$user->avatar
        ];

        $response = $this->actingAs($user)->hitUpdateProfileEndpoint($user, $userData);
//dd($response);
        $response->assertStatus(200);

        $this->assertDatabaseHas('users',$userData);

    }

}
