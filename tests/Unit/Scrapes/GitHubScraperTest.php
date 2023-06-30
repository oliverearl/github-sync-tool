<?php

declare(strict_types=1);

use App\Objects\ContributionSquare;
use App\Scrapes\GitHubScraper;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

it('will return an array of contribution graphs from ', function () {
    Http::fake([
        '*' => Http::response(getGitHubMock(), Response::HTTP_OK),
    ]);

    $data = app(GitHubScraper::class)->scrape(Str::random(), '2022');

    expect($data)
        ->toBeArray()
        ->not()->toBeEmpty()
        ->and(count($data))->toBeGreaterThanOrEqual(100)
        ->and(head($data))->toBeInstanceOf(ContributionSquare::class);
});

it('will throw an exception if the username does not exist', function () {
    Http::fake([
        '*' => Http::response('Not found', Response::HTTP_NOT_FOUND),
    ]);

    app(GitHubScraper::class)->scrape(Str::random(), date('Y'));
})->throws(RequestException::class);

it('will throw an exception if the server is down', function () {
    Http::fake([
        '*' => Http::response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR),
    ]);

    app(GitHubScraper::class)->scrape(Str::random(), date('Y'));
})->throws(RequestException::class);
