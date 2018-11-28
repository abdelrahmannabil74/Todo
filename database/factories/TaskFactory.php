<?php

use Faker\Generator as Faker;


$factory->define(App\Task::class, function (Faker $faker) {
    return [
        'title' => $faker->name,
        'is_private' => $faker->boolean,
        'is_complete'=> $faker->boolean,
        'deadline' => $faker->dateTime->format('Y-m-d H:i:s'),
        'user_id' => function () {
            return factory(\App\User::class)->create()->id;
        },

    ];
});
