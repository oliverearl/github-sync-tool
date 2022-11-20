<?php

declare(strict_types=1);

namespace App\Scrapes;

use App\Objects\ContributionSquare;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class GitHubScraper
{
    public const GITHUB_URL = 'https://www.github.com/';

    /**
     * Scrape a given GitHub profile.
     *
     * @param string $username
     * @param string $year
     *
     * @return array
     */
    public function scrape(string $username, string $year): array
    {
        $graph = [];
        $url = self::GITHUB_URL . $username;

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::throw()->get($url, [
            'tab' => 'overview',
            'from' => "{$year}-01-01",
            'to' => "{$year}-12-31",
        ]);

        $contributionGraph = app(Crawler::class, ['node' => $response->body(), 'uri' => $url])
            ->filter('svg')
            ->filter('.js-calendar-graph-svg')
            ->children('g')
            ->first()
            ->children('g')
            ->children('rect');

        $contributionGraph->each(static function (Crawler $square) use (&$graph) {
            $count = $square->attr('data-count');
            $date = $square->attr('data-date');

            if ($count > 0) {
                $graph[] = new ContributionSquare(
                    date: $date,
                    count: (int) $count,
                );
            }
        });

        return $graph;
    }
}
