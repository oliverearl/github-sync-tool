<?php

declare(strict_types=1);

namespace App\Exports;

class BashScriptExporter
{
    public function write(array $data, string $filename, string $name, string $email): void
    {
        if (! file_exists($filename)) {
            touch($filename);
        }

        $f = fopen($filename, 'wb');
        fwrite($f, '#!/bin/bash' . PHP_EOL);

        // Write the Git author details:
        fwrite($f, sprintf('git config user.name "%s"%s', $name, PHP_EOL));
        fwrite($f, sprintf('git config user.email %s%s', $email, PHP_EOL));

        /** @var \App\Objects\ContributionSquare $contribution */
        foreach ($data as $contribution) {
            $date = $contribution->getDate();

            for ($i = 0, $iMax = $contribution->getCount(); $i < $iMax; $i++) {
                $count = $i;
                $command = sprintf(
                    'GIT_AUTHOR_DATE=%sT12:00:00 GIT_COMMITTER_DATE=%sT12:00:00 git commit --allow-empty -m "%s" > /dev/null%s',
                    $date,
                    $date,
                    "Contributed work on {$date} - Commit #" . ++$count,
                    PHP_EOL,
                );

                fwrite($f, $command);
            }
        }

        fclose($f);
        chmod($filename, 0755);
    }
}
