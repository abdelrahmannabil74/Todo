<?php

namespace Tests\Feature\User;

use App\Task;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchUserTest extends TestCase
{
    private function hitSearchEndPoint($user)
    {
        $response = $this->get(route('search' ,$user));
        return $response;
    }


     /**
      * @test
      */
     function test_a_user_can_search_for_user()
     {
         $user= factory(User::class)->create();

         $this->actingAs($user);

         $response = $this->hitSearchEndPoint($user);

         $response->assertStatus(200);

         $this->assertDatabaseHas('users',$user->toArray());

     }


}
