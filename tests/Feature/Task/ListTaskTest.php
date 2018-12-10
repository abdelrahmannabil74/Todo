<?php

namespace Tests\Feature\Task;

use App\Task;
use App\Transformers\TaskTransformer;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Spatie\Fractal\Fractal;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListTaskTest extends TestCase
{
    private function hitShowTaskEndpoint($task): TestResponse
    {
        $response = $this->get(route('listTasks' ,$task));
        return $response;
    }

    /**
     * @param TestResponse $response
     * @param Task $task
     */

    private function assertJsonResponseHasTask(TestResponse $response,$tasks)
    {
         $response->assertJson(\Fractal::collection($tasks,new TaskTransformer())->toArray());

    }

    /**@test */
    function test_authenticated_user_can_view_tasks()
    {
        $this->actingAs(factory(User::class)->create());

        $task=Task::where('user_id',\Auth::id())->orWhere('is_private',0);

        $taskData=factory(Task::class)->make()->toArray();

        $response = $this->hitShowTaskEndpoint($taskData);
//dd($response);
        $response->assertStatus(200);

        $this->assertJsonResponseHasTask($response,$task);

    }

    /**
     * @test
     */
    function test_unauthorized_user_can_view_public_tasks_from_other_users()
    {
        $this->actingAs(factory(User::class)->create());

        $task=Task::where('user_id',\Auth::id())->orWhere('is_private',0);

        $taskData=factory(Task::class)->make()->toArray();

        $response = $this->hitShowTaskEndpoint($taskData);

        $response->assertStatus(200);

       $this->assertJsonResponseHasTask($response,$task);

    }

    /**
     * @test
     */
    function test_guest_can_only_view_public_tasks()
    {
        factory(User::class)->create();

        $task = factory(Task::class)->make()->toArray();

        $response = $this->hitShowTaskEndpoint($task);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);

    }

}
