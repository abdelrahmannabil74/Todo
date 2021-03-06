<?php

namespace Tests\Feature\Task;

use App\Task;
use App\Transformers\TaskTransformer;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTaskTest extends TestCase
{
//    use DatabaseMigrations;
    /**
     * A data provider for the non-required fields to update a task in definition step.
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
     * A data provider for the required fields to update a task in update endpoint.
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
     * A data provider for the invalid values of fields to update a task in update endpoint.
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

    private function hitUpdateTaskEndpoint($task, $taskData): TestResponse
    {
        $response = $this->put(route('updateTask', ['task' => $task->id]), $taskData);
        return $response;
    }


    /**
     * @param TestResponse $response
     * @param Task $task
     */

    private function assertJsonResponseHasTask(TestResponse $response,Task $task)
    {
        $task->all()->last();

        $response->assertJson(\Fractal::item($task, new TaskTransformer())->toArray());

    }



    /**@test */

    public function test_authenticated_user_can_update_task()
    {
        $user=factory(User::class)->create();

        $this->actingAs($user);

        $taskToBeUpdated = factory(Task::class)->create(['user_id'=>$user->id]);

        $taskData = factory(Task::class)->make()->toArray();

        $response = $this->hitUpdateTaskEndpoint($taskToBeUpdated, $taskData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks',$taskData);

        $this->assertJsonResponseHasTask($response,$taskToBeUpdated->fresh());
    }


    /**@test */
    function test_unauthenticated_user_cant_update_other_users_tasks()
    {
        $user= factory(User::class)->create();

        $user_not_authorized= $this->actingAs(factory(User::class)->create());

        $taskToBeUpdated = factory(Task::class)->create(['user_id'=>$user->id]);

        $taskData = factory(Task::class)->make()->toArray();

        $response = $user_not_authorized->hitUpdateTaskEndpoint($taskToBeUpdated,$taskData);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);
    }


    /**@test */
    function test_guest_cant_update_task()
    {

        $task = factory(Task::class)->create();

        $taskData = factory(Task::class)->make()->toArray();

        $response = $this->hitUpdateTaskEndpoint($task, $taskData);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);

    }
//

    /**
     * @dataProvider requiredFields
     * @param $field
     * @test*/
    function test_user_cant_update_task_without_required_fields($field)
    {

        $user_factory=factory(User::class)->create();

        $taskToBeUpdated = factory(Task::class)->create(['user_id'=>$user_factory->id]);

        $taskData = factory(Task::class)->make()->toArray();

        array_set($taskData, $field, null);

        $response = $this->actingAs($user_factory)->hitUpdateTaskEndpoint($taskToBeUpdated, $taskData);

         $response->assertStatus(422);

        $errors = $response->decodeResponseJson('errors');

        $this->assertArrayHasKey($field, $errors);

    }


    /**
     * @dataProvider invalidFieldsValues
     * @param $field
     * @param $value
     * @test*/
    function test_user_cant_update_task_with_invalid_fields($field,$value)
    {

        $user_factory=factory(User::class)->create();

        $taskToBeUpdated = factory(Task::class)->create(['user_id'=>$user_factory->id]);

        $taskData = factory(Task::class)->make()->toArray();

        array_set($taskData,$field,$value);

        $response = $this->actingAs($user_factory)->hitUpdateTaskEndpoint($taskToBeUpdated, $taskData);

        $response->assertStatus(422);

        $errors = $response->decodeResponseJson('errors');

        $this->assertArrayHasKey($field, $errors);

    }



}
