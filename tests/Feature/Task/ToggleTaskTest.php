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

class ToggleTaskTest extends TestCase
{
//    use DatabaseMigrations;

    /**
     * A data provider for the required fields to toggle a task in toggle endpoint.
     *
     * @return array
     */
    public function requiredFields()
    {
        return [


            ['is_complete'],

            ];
    }

    /**
     * A data provider for the invalid values of fields to toggle a task in toggle endpoint.
     *
     * @return array
     */
    public function invalidFieldsValues()
    {
        return [


            ['is_complete',22],
            ['is_complete','string'],


        ];
    }

    private function hitToggleTaskEndpoint($task, $taskData): TestResponse
    {
        $response = $this->put(route('toggleStatus', ['task' => $task->id]), $taskData);
        return $response;
    }

    /**
     * @param TestResponse $response
     * @param Task $task
     */

    private function assertJsonResponseHasTask(TestResponse $response,Task $task)
    {

        $response->assertJson(\Fractal::item($task, new TaskTransformer())->toArray());

    }


    /**@test */

    public function test_authenticated_user_can_toggle_his_own_task_status()
    {
        $user_factory=factory(User::class)->create();

        $taskToBeToggled = factory(Task::class)->create(['user_id'=>$user_factory->id]);

        $taskData = ['is_complete' => true];

        $response = $this->actingAs($user_factory)->hitToggleTaskEndpoint($taskToBeToggled, $taskData);

        $response->assertStatus(200);

        $taskToBeToggled->is_complete = $taskData['is_complete'];

        $this->assertDatabaseHas('tasks',$taskToBeToggled->toArray());

        $this->assertJsonResponseHasTask($response,$taskToBeToggled->fresh());

    }

    /**@test */
    function test_unauthenticated_user_cant_toggle_the_status_other_users_tasks()
    {
        $user= factory(User::class)->create();

        $user_not_authorized= $this->actingAs(factory(User::class)->create());

        $taskToBeUpdated = factory(Task::class)->create(['user_id'=>$user->id]);

        $taskData = factory(Task::class)->make()->toArray();

        $response = $user_not_authorized->hitToggleTaskEndpoint($taskToBeUpdated,$taskData);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);


    }

    /**@test */
    function test_guest_cant_toggle_task_status()
    {

        $task = factory(Task::class)->create();

        $taskData = factory(Task::class)->make()->toArray();

        $response = $this->hitToggleTaskEndpoint($task, $taskData);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);

    }



    /**
     * @dataProvider requiredFields
     * @param $field
     * @test*/
    function test_user_cant_toggle_task_status_without_required_fields($field)
    {

        $user_factory=factory(User::class)->create();

        $taskToBeToggled = factory(Task::class)->create(['user_id'=>$user_factory->id]);

        $taskData = ['is_complete' => true];

        array_set($taskData, $field, null);

        $response = $this->actingAs($user_factory)->hitToggleTaskEndpoint($taskToBeToggled, $taskData);

        $response->assertStatus(422);

        $errors = $response->decodeResponseJson('errors');

        $this->assertArrayHasKey($field, $errors);


    }


    /**
     * @dataProvider invalidFieldsValues
     * @param $field
     * @param $value
     * @test*/
    function test_user_cant_toggle_task_status_with_invalid_fields($field,$value)
    {

        $user_factory=factory(User::class)->create();

        $taskToBeUpdated = factory(Task::class)->create(['user_id'=>$user_factory->id]);

        $taskData = ['is_complete' => true];

        array_set($taskData,$field,$value);

        $response = $this->actingAs($user_factory)->hitToggleTaskEndpoint($taskToBeUpdated, $taskData);

        $response->assertStatus(422);

        $errors = $response->decodeResponseJson('errors');

        $this->assertArrayHasKey($field, $errors);

    }


}
