<?php

declare(strict_types=1);

namespace App\Commands;

use App\Exports\BashScriptExporter;
use App\Objects\ContributionSquare;
use App\Scrapes\GitHubScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class SyncGitHubCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'github-sync
                        {from : The username of the GitHub profile to sync commits from (required)}
                        {year : The year to sync (required)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sync commits from a given GitHub account to another.';

    /**
     * Scraper used for scraping contributions from GitHub.
     *
     * @var \App\Scrapes\GitHubScraper
     */
    private GitHubScraper $scraper;

    /**
     * Exporter used for writing the Bash script.
     *
     * @var \App\Exports\BashScriptExporter
     */
    private BashScriptExporter $exporter;

    /**
     * The Git author name.
     *
     * @var string
     */
    private string $authorName;

    /**
     * The Git author email address.
     *
     * @var string
     */
    private string $authorEmail;

    /**
     * The successful scrape data.
     *
     * @var array
     */
    private array $scrape;

    /**
     * The full path to the newly created script.
     *
     * @var string
     */
    private string $filename;

    /**
     * Construct the command to begin the scraping process.
     *
     * @param \App\Scrapes\GitHubScraper $scraper
     * @param \App\Exports\BashScriptExporter $exporter
     *
     * @return void
     */
    public function __construct(GitHubScraper $scraper, BashScriptExporter $exporter)
    {
        parent::__construct();

        $this->scraper = $scraper;
        $this->exporter = $exporter;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $this->validateArguments();
            $this->assignAuthorDetails();
            $this->scrape();
            $this->export();
            $this->promptToExecute();
        } catch (Throwable $throwable) {
            $this->error('An error occurred during the scrape: The following problem was encountered:');
            $this->error($throwable->getMessage() ?? 'An unknown error has occurred.');

            return 1;
        }

        $this->info('Goodbye!');

        return 0;
    }

    /**
     * Validates the arguments provided to the command.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function validateArguments(): void
    {
        if (empty($this->argument('from'))) {
            throw new InvalidArgumentException('GitHub username cannot be empty.');
        }

        if (Str::contains($this->argument('from'), ['http', 'www', '@'], true)) {
            throw new InvalidArgumentException('Username must not be a web address or including the @ symbol');
        }

        if ((int) $this->argument('year') > (int) date('Y')) {
            throw new InvalidArgumentException('Year must be this year or below. I am not a time traveller.');
        }
    }

    /**
     * Collect the username and email address to use for the commits.
     *
     * @return void
     */
    protected function assignAuthorDetails(): void
    {
        $this->authorName = $this->ask('What name do you wish to attribute the commits to?');
        $this->authorEmail = $this->ask('What email address do you wish to attribute the commits to?');

        if (empty($this->authorName) || empty($this->authorEmail)) {
            throw new InvalidArgumentException('Name and email address cannot be empty.');
        }
    }

    /**
     * Perform the scrape.
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function scrape(): void
    {
        $this->info('Retrieving data from GitHub...');

        $result = $this->scraper->scrape($this->argument('from'), $this->argument('year'));

        $days = count($result);
        $contributions = collect($result)->map(static fn (ContributionSquare $s) => $s->getCount())->sum();

        if (! $this->confirm(
            question: "A total of {$contributions} commits across {$days} days were found. Does this look correct?",
            default: true,
        )) {
            throw new RuntimeException('Consent is required to write commits.');
        }

        $this->scrape = $result;
    }

    /**
     * Export data to a Bash script.
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function export(): void
    {
        $this->info('I will now begin writing a Bash script that will be placed in this directory.');

        $this->filename = sprintf(
            '%s%s%s_%s.sh',
            base_path(),
            DIRECTORY_SEPARATOR,
            $this->argument('from'),
            $this->argument('year'),
        );

        if (! is_writable($this->filename)) {
            throw new RuntimeException('This directory is not writeable, cannot continue!');
        }

        $this->exporter->write(
            data: $this->scrape,
            filename: $this->filename,
            name: $this->authorName,
            email: $this->authorEmail,
        );
    }

    protected function promptToExecute(): void
    {
        if (PHP_OS === 'WINNT') {
            $this->warn('Cannot execute a Bash script (just yet) on native Windows, so my job here is done!');

            return;
        }

        if (! $this->ask('Would you like me to attempt to execute the script here, in this repository?')) {
            $this->info('Okay! Your script has been successfully generated.');
        }

        $this->info('Please wait...');
        $process = new Process(['bash ' . $this->filename]);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info('Process complete! The commits have been processed to this repository.');

        if ($this->ask('Would you like me to delete the script?')) {
            unlink($this->filename);
        }
    }
}
