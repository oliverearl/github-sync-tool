<?php

declare(strict_types=1);

use App\Objects\ContributionSquare;

it('can be created and its data can be accessed through accessors', function () {
    $count = 50;
    $date = date('Y');

    $square = new ContributionSquare($date, $count);

    expect($square)
        ->toBeInstanceOf(ContributionSquare::class)
        ->and($square->getCount())
            ->toBe($count)
        ->and($square->getDate())
            ->toBe($date)
        ->and($square->toArray())
            ->toBeArray()
            ->toBe(['date' => $date, 'count' => $count]);
});

it('will throw an exception if the date provided is not a real date', function () {
   $date = 'Not a date';
   $count = 10;

   $square = new ContributionSquare($date, $count);
})->throws(InvalidArgumentException::class);
