<?php

namespace Tests\Feature\Task;

use App\Task;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTaskTest extends TestCase
{

    private function hitDeleteTaskEndpoint($task): TestResponse
    {

        $response =$this->delete(route('deleteTask',['task'=> $task->id]));
        return $response;
    }


    function test_authenticated_user_can_delete_task()
    {
        $user_factory=factory(User::class)->create();

        $task= factory(Task::class)->create(['user_id'=>$user_factory->id]);

        $taskData=factory(Task::class)->make()->toArray();

        $response = $this->actingAs($user_factory)->hitDeleteTaskEndpoint($task);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks',$taskData);

        $response->assertJsonMissing($taskData);



    }

    /**@test */
    function test_unauthenticated_user_cant_delete_other_users_tasks()
    {
        $user= factory(User::class)->create();

        $user_not_authorized= $this->actingAs(factory(User::class)->create());

        $taskToBeDeleted = factory(Task::class)->create(['user_id'=>$user->id]);

        $response = $user_not_authorized->hitDeleteTaskEndpoint($taskToBeDeleted);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);



    }


    /**@test */
    function test_guest_cant_delete_task()
    {
        $task=factory(Task::class)->create();

        $response = $this->hitDeleteTaskEndpoint($task);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);


    }


}
