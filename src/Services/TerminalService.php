<?php declare(strict_types=1);

namespace VitesseCms\Cli\Services;

class TerminalService implements TerminalServiceInterface
{
    public function printHeader(string $header): void
    {
        echo '=== ' . $header . ' ===' . PHP_EOL;
    }

    public function printError(string $error): void
    {
        echo "\e[0;31mError:\e[0m " . $error . PHP_EOL;
    }

    public function printMessage(string $message): void
    {
        echo "\e[0;36mMessage:\e[0m " . $message . PHP_EOL;
    }
}