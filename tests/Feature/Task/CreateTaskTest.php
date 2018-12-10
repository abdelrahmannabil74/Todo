<?php

namespace Tests\Feature\Task;

use App\Task;
use App\Transformers\TaskTransformer;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTaskTest extends TestCase
{

    /**
     * A data provider for the required fields in the store endpoint.
     *
     * @return array
     */
    public function requiredFields()
        {
             return [


                 ['title'],
                 ['is_private'],
                 ['deadline'],
                 ['user_id'],
             ];
        }



    /**
     * A data provider for the invalid fields in the store endpoint.
     *
     * @return array
     */
    public function invalidFields()
    {
        return [

                ['is_private','string'],
                ['is_private',22],

                ['deadline','string'],
                ['deadline',22],
                ['deadline',false],

            ];
    }


    /**
     * Sends a post request to create task endpoint.
     *
     * @param $task
     *
     * @return TestResponse
     */
    private function hitCreateTaskEndpoint($task): TestResponse
    {
        $response = $this->post(route('createTask', $task));
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


    /**
     * @test
    */
    function test_authenticated_user_can_create_task()
    {
        $user=factory(User::class)->create();

        $this->actingAs($user);

        $task=factory(Task::class)->create();

        $taskData=factory(Task::class)->make()->toArray();

        $response = $this->hitCreateTaskEndpoint($taskData);

        $response->assertStatus(201);

        $taskData['user_id'] = $user->id;

        $this->assertDatabaseHas('tasks',$taskData);

         $this->assertJsonResponseHasTask($response,Task::all()->last());
    }

    /**
     * @test
     */
    function test_guest_cant_create_task()
    {

        $taskData = factory(Task::class)->make()->toArray();

        $response = $this->hitCreateTaskEndpoint($taskData);

        $response->assertStatus(401);

        $response->assertJson(['errors'=>'Forbidden!']);

    }

    /**
     * @dataProvider  requiredFields
     *
     * @test
     * @param $field
     *
     */
    function test_user_cant_create_task_without_required_fields($field)
    {

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $taskData = factory(Task::class)->make()->toArray();

        unset($taskData[$field]);

        $response = $this->hitCreateTaskEndpoint($taskData);

        $response->assertStatus(422);

        $errors = $response->decodeResponseJson('errors');

        $this->assertArrayHasKey($field, $errors);

    }

    /**
     * @dataProvider invalidFields
     * @test
     * @param $field
     * @param $value
     */
    function test_user_cant_create_task_with_invalid_fields($field,$value)
    {

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $taskData = factory(Task::class)->make()->toArray();

        array_set($taskData,$field,$value);

        $response = $this->hitCreateTaskEndpoint($taskData);

        $response->assertStatus(422);

        $errors = $response->decodeResponseJson('errors');

        $this->assertArrayHasKey($field, $errors);

    }


}


