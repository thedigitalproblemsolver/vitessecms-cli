<?php declare(strict_types=1);

namespace VitesseCms\Cli;

use Exception;
use Phalcon\Cli\Console as ConsoleApp;
use Throwable;
use VitesseCms\Cli\Utils\CliUtil;

require_once __DIR__ . '/../../../autoload.php';
require_once __DIR__ . '/../../configuration/src/Utils/DomainConfigUtil.php';
require_once __DIR__ . '/../../core/src/Utils/DirectoryUtil.php';
require_once __DIR__ . '/../../core/src/Utils/FileUtil.php';
require_once __DIR__ . '/../../core/src/Utils/DebugUtil.php';
require_once __DIR__ . '/../../configuration/src/Utils/AccountConfigUtil.php';
require_once __DIR__ . '/../../configuration/src/Services/ConfigServiceInterface.php';
require_once __DIR__ . '/../../configuration/src/Services/ConfigService.php';
require_once __DIR__ . '/../../core/src/Services/UrlService.php';
require_once __DIR__ . '/Utils/CliUtil.php';
require_once __DIR__ . '/BootstrapCli.php';

if (count($argv) < 4) {
    echo 'A argument is missing' . PHP_EOL;
    die();
}

$_SERVER['HTTP_HOST'] = $argv[3];
$_SERVER['SERVER_ADDR'] = '192.167.0.33';

$di = new BootstrapCli();
$di->setUrl();
if($argv[1] !== 'install' && $argv[2] !== 'create' ) :
    $di->loadConfig();
    $di->loaderSystem();
    $di->database();
    $di->view();
elseif($argv[1] === 'domain' && $argv[2] === 'create') :
    require __DIR__ . '/Tasks/DomainTask.php';
else :
    require __DIR__ . '/Tasks/InstallTask.php';
endif;

$console = new ConsoleApp();
$console->setDI($di);

try {
    $console->handle(CliUtil::buildArguments($argv));
} catch (\Phalcon\Exception $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
} catch (Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
