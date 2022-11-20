<?php

declare(strict_types=1);

namespace App\Exports;

class BashScriptExporter
{
    public function write(array $data, string $filename, string $name, string $email): void
    {
        touch($filename);
    }
}
