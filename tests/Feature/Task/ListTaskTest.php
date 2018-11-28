<?php

namespace Tests\Feature\Task;

use App\Task;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
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

    /**@test */
    function test_authenticated_user_can_view_tasks()
    {
        $this->actingAs(factory(User::class)->create());

        $task=factory(Task::class)->make()->toArray();

        $response = $this->hitShowTaskEndpoint($task);

        $response->assertStatus(200);

    }

    /**@tess */
    function test_unauthenticated_user_can_view_public_tasks_from_other_users()
    {
        $user= factory(User::class)->create();

        $user_not_authorized= $this->actingAs(factory(User::class)->create());

        $task = factory(Task::class)->create(['user_id'=>$user->id]);

        $response = $user_not_authorized->hitShowTaskEndpoint($task);

        $response->assertStatus(200);

    }

    /**@test */
    function test_guest_can_only_view_public_tasks()
    {
        factory(User::class)->create();

        $task = factory(Task::class)->make()->toArray();

        $response = $this->hitShowTaskEndpoint($task);

        $response->assertStatus(401);

    }


}
