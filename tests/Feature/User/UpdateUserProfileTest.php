<?php

namespace Tests\Feature\User;

use App\Transformers\UserTransformer;
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

            ['info'],
            ['avatar'],
            ];
    }

    /**
     * A data provider for the invalid values of fields to update a user in updateProfile endpoint.
     *
     * @return array
     */
    public function invalidFieldsValues()
    {
        return [

            ['info',22],
            ['info',false],

            ['avatar',22],
            ['avatar',false],

            ];

    }


    /**
     *
     * @test
     */
    function authenticated_user_can_update_his_profile_data()
    {
        $user=factory(User::class)->create();

        $userData = [
            'info'=>$user->info,
            'avatar'=>$user->avatar
        ];

        $response = $this->actingAs($user)->hitUpdateProfileEndpoint($user, $userData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users',$userData);

        $user->all()->last();

        $response->assertJson(\Fractal::item($user, new UserTransformer())->toArray());

    }

    /**
     * @test
    */
    function guest_cant_update_profile()
    {
        $user=factory(User::class)->create();

        $userData = [
            'info'=>$user->info,
            'avatar'=>$user->avatar
        ];

       $response= $this->hitUpdateProfileEndpoint($user, $userData);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);
    }

    /**
     * @dataProvider requiredFields
     * @test
     * @param $field
    */
    function a_user_cant_update_his_profile_without_required_fields($field)
    {
        $user=factory(User::class)->create();

        $this->actingAs($user);

        $userData = [
            'info'=>$user->info,
            'avatar'=>$user->avatar
        ];

        unset($userData[$field]);

        $response =$this->hitUpdateProfileEndpoint($user, $userData);

        $response->assertStatus(422);

        $errors = $response->decodeResponseJson('errors');

        $this->assertArrayHasKey($field, $errors);

    }

    /**
     * @dataProvider invalidFieldsValues
     * @test
     * @param $field
     * @param $value
    */

    function test_a_user_cant_update_his_profile_with_invalid_fields($field,$value)
    {
        $user=factory(User::class)->create();

        $this->actingAs($user);

        $userData = [
            'info'=>$user->info,
            'avatar'=>$user->avatar
        ];

        array_set($userData,$field,$value);

        $response =$this->hitUpdateProfileEndpoint($user, $userData);

        $response->assertStatus(422);

        $errors = $response->decodeResponseJson('errors');

        $this->assertArrayHasKey($field, $errors);

    }


}
