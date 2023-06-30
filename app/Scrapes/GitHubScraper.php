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
     * Needle to be used to determine contribution quantity.
     *
     * @var string
     */
    final public const NODE_NEEDLE = ' contributions';

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

            if ($date !== null && $hasData !== '0') {
                // The value is sometimes wrapped in a span. Search without it first.
                $contributionCount = Str::before($square->innerText(), self::NODE_NEEDLE) ?: null;
                $contributionCount ??= Str::before($square->filter('span')?->first()?->innerText(), self::NODE_NEEDLE) ?: null;

                // Convert the value to a number, including null, which becomes zero.
                $contributionCount = (int) $contributionCount;

                if ($contributionCount > 0) {
                    $graph[] = new ContributionSquare(
                        date: $date,
                        count: $contributionCount,
                    );
                }
            }
        });

        return $graph;
    }
}
