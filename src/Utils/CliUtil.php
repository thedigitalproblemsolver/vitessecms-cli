<?php declare(strict_types=1);

namespace VitesseCms\Cli\Utils;

class CliUtil
{
    public static function buildArguments(array $args): array
    {
        $arguments = [];

        foreach ($args as $k => $arg) {
            switch ($k) :
                case 1:
                    $arguments['task'] = 'VitesseCms\Cli\Tasks\\'.ucfirst($arg);
                    break;
                case 2:
                    $arguments['action'] = $arg;
                    break;
                case 3:
                    $arguments['domain'] = $arg;
                    break;
            endswitch;
        }

        return $arguments;
    }
}
