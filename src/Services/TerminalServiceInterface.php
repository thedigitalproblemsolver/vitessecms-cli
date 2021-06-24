<?php declare(strict_types=1);

namespace VitesseCms\Cli\Services;

interface TerminalServiceInterface
{
    public function printHeader(string $header): void;

    public function printError(string $error): void;

    public function printMessage(string $message): void;
}