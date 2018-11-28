<?php

namespace Tests\Feature\Task;

use App\Task;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTaskTest extends TestCase
{
//    use DatabaseMigrations;

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
     * Asserts that task has been updated appropriately in the DB.
     *
     * @param $valuesUpdated
     * @param $task
     */
    private function assertTaskUpdatedInDB($valuesUpdated, $task): void
    {
        foreach ($valuesUpdated as $key => $item) {
            assertEquals($item, $task->fresh()->$key);
        }
    }



    /** @test */
    function test_authenticated_user_can_create_task()
    {
        $this->actingAs(factory(User::class)->create());

        $task=factory(Task::class)->make()->toArray();

        $response = $this->hitCreateTaskEndpoint($task);

        $response->assertStatus(201);

    }

    /**@test */
    function test_guest_cant_create_task()
    {

        $taskData = factory(Task::class)->make()->toArray();

        $response = $this->hitCreateTaskEndpoint($taskData);

        $response->assertStatus(401);

    }

    /**@test
     *  @dataProvider  requiredFields
     */
    function test_user_cant_create_task_without_required_fields()
    {
        $this->actingAs(factory(User::class)->create());

        factory(Task::class)->make()->toArray();

        $response = $this->hitCreateTaskEndpoint([]);

        $response->assertStatus(406);
    }


    /**
     * @dataProvider invalidFields
     * @test */
    function test_user_cant_create_task_with_invalid_fields($field,$value)
    {

        $user_factory=factory(User::class)->create();

        $taskData = factory(Task::class)->make()->toArray();

        array_set($taskData,$field,$value);

        $response = $this->actingAs($user_factory)->hitCreateTaskEndpoint($taskData);

        $response->assertStatus(406);
    }


}


