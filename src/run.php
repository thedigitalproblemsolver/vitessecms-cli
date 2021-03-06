<?php declare(strict_types=1);

namespace VitesseCms\Cli;

use Exception;
use Phalcon\Cli\Console as ConsoleApp;
use VitesseCms\Cli\Utils\CliUtil;

require __DIR__ . '/../../../autoload.php';
require __DIR__ . '/../../configuration/src/Utils/DomainConfigUtil.php';
require __DIR__ . '/../../core/src/Utils/DebugUtil.php';
require __DIR__ . '/../../configuration/src/Utils/AccountConfigUtil.php';
require __DIR__ . '/../../configuration/src/Services/ConfigService.php';
require __DIR__ . '/../../core/src/Services/UrlService.php';
require __DIR__ . '/Utils/CliUtil.php';
require __DIR__ . '/BootstrapCli.php';

if (count($argv) < 4) {
    echo 'A argument is missing' . PHP_EOL;
    die();
}

$_SERVER['HTTP_HOST'] = $argv[3];

$di = new BootstrapCli();
$di->setUrl();
if($argv[1] !== 'install' && $argv[2] !== 'create' ) :
    $di->loadConfig();
    $di->loaderSystem();
else :
    require __DIR__ . '/Tasks/DomainTask.php';
endif;

$console = new ConsoleApp();
$console->setDI($di);

try {
    $console->handle(CliUtil::buildArguments($argv));
} catch (\Phalcon\Exception $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (\Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
} catch (Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
