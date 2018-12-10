<?php

namespace Tests\Feature\User;

use App\Task;
use App\Transformers\TaskTransformer;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListUserNewsFeedTest extends TestCase
{

    private function hitShowNewsFeedEndpoint($task): TestResponse
    {
        $response = $this->get(route('newsFeed' ,$task));
        return $response;
    }


    /**
     * @test
     */
    function test_authenticated_user_can_view_news_feed()
    {
        $user=factory(User::class)->create();

        $this->actingAs($user);

//        $task=factory(Task::class)->create();

        $task=Task::where('user_id',\Auth::user()->id);

        $task=$task->orderBy('created_at','desc')->with('user')->get();

        $response=$this->hitShowNewsFeedEndpoint($task);

        $response->assertStatus(200);


        $response->assertJson(\Fractal::includes('user')->collection($task,new TaskTransformer())->toArray());

    }


    /**
     * @test
     */

    function test_a_guest_cant_view_news_feed()
    {
        factory(User::class)->create();

        $task = factory(Task::class)->make()->toArray();

        $response = $this->hitShowNewsFeedEndpoint($task);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);

    }

}
