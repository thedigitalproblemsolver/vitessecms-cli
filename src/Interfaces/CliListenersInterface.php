<?php

declare(strict_types=1);

namespace VitesseCms\Cli\Interfaces;

use VitesseCms\Cli\ConsoleApplication;

interface CliListenersInterface
{
    public static function setListeners(ConsoleApplication $di): void;
}
