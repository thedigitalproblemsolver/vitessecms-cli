<?php declare(strict_types=1);

namespace VitesseCms\Cli;

use Exception;
use VitesseCms\Cli\Utils\CliUtil;
use Phalcon\Cli\Console as ConsoleApp;

require __DIR__ . '/../../../autoload.php';
require __DIR__ . '/../../vitessecms/src/configuration/utils/AbstractConfigUtil.php';
require __DIR__ . '/../../vitessecms/src/configuration/utils/DomainConfigUtil.php';
require __DIR__ . '/../../vitessecms/src/core/utils/DebugUtil.php';
require __DIR__ . '/../../vitessecms/src/configuration/utils/AccountConfigUtil.php';
require __DIR__ . '/../../vitessecms/src/core/services/ConfigService.php';
require __DIR__ . '/../../vitessecms/src/core/services/UrlService.php';
require 'BoostrapCli.php';

if (count($argv) < 4) {
    echo 'A argument is missing'.PHP_EOL;
    die();
}

$_SERVER['HTTP_HOST'] = $argv[3];

$di = new BoostrapCli();
$di->setUrl();
$di->loadConfig();
$di->loaderSystem();

$console = new ConsoleApp();
$console->setDI($di);

try {
    $console->handle(CliUtil::buildArguments($argv));
} catch (\Phalcon\Exception $e) {
    fwrite(STDERR, $e->getMessage().PHP_EOL);
    exit(1);
} catch (\Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage().PHP_EOL);
    exit(1);
} catch (Exception $exception) {
    fwrite(STDERR, $exception->getMessage().PHP_EOL);
    exit(1);
}
