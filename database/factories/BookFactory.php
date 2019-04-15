<?php

use Faker\Generator as Faker;

$factory->define(App\Book::class, function (Faker $faker) {
    $title = $faker->sentence();
    $slug = str_slug($title);

    return [
        'author_id' => function () {
            return factory(App\Author::class)->create()->id;
        },
        'language_id' => function () {
            return factory(App\Language::class)->create()->id;
        },
        'original_language_id' => function () {
            return factory(App\Language::class)->create()->id;
        },
       'title' => $title,
        'slug' => $slug,
        'year_of_publish'  => $faker->numberBetween(1900,2019),
    ];
});
