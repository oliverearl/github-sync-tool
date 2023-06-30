<?php

declare(strict_types=1);

namespace App\Scrapes;

use App\Objects\ContributionSquare;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class GitHubScraper
{
    /**
     * The GitHub URL used for the scraper.
     *
     * @var string
     */
    final public const GITHUB_URL = 'https://www.github.com/';

    /**
     * The tab name used for harvesting data.
     *
     * @var string
     */
    final public const TAB_NAME = 'overview';

    /**
     * Scrape a given GitHub profile.
     *
     * @param string $username
     * @param string $year
     *
     * @return array<int, \App\Objects\ContributionSquare>
     */
    public function scrape(string $username, string $year): array
    {
        $graph = [];
        $url = self::GITHUB_URL . $username;

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::throw()->get($url, [
            'tab' => self::TAB_NAME,
            'from' => "{$year}-01-01",
            'to' => "{$year}-12-31",
        ]);

        $contributionGraph = app(Crawler::class, ['node' => $response->body(), 'uri' => $url])
            ->filter('.ContributionCalendar-day');

        $contributionGraph->each(static function (Crawler $square) use (&$graph): void {
            $date = $square->attr('data-date');
            $hasData = $square->attr('data-level');

            if ($hasData !== '0') {
                $contributionCount = (int) Str::before($square->innerText(), ' contributions');

                if ($contributionCount > 0) {
                    $graph[] = new ContributionSquare(
                        date: $date,
                        count: $contributionCount,
                    );
                }
            }
        });
    }
}
