<?php

declare(strict_types=1);

use App\Exports\BashScriptExporter;
use App\Objects\ContributionSquare;

it('can write a bash script', function () {
    $date = date('Y-m-d');
    $count = 1;

    $data = [
        new ContributionSquare($date, $count),
    ];

    $filename = base_path('test.sh');
    $name = 'John Doe';
    $email = 'john.doe@example.com';

    app(BashScriptExporter::class)->write(
        data: $data,
        filename: $filename,
        name: $name,
        email: $email,
    );

    expect($filename)->toBeWritableFile();

    $f = fopen($filename, 'rb');

    expect(fgets($f))->toContain('#!/bin/bash')
        ->and(fgets($f))->toContain('git config user.name "John Doe"')
        ->and(fgets($f))->toContain('git config user.email john.doe@example.com')
        ->and(fgets($f))
            ->toContain('GIT_AUTHOR_DATE')
            ->toContain('GIT_COMMITTER_DATE')
            ->toContain('git commit')
            ->toContain('Commit #1');

    if (file_exists($filename)) {
        unlink($filename);
    }
});
