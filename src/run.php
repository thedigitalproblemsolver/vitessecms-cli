<?php
declare(strict_types=1);

namespace VitesseCms\Cli;

use Dotenv\Dotenv;
use Exception;
use Throwable;
use VitesseCms\Cli\Utils\CliUtil;
use VitesseCms\Core\Utils\DirectoryUtil;
use Phalcon\Http\Request;

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
require_once __DIR__ . '/ConsoleApplication.php';

if (count($argv) < 4) {
    echo 'A argument is missing' . PHP_EOL;
    die();
}

$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../../../../');
$dotenv->load();

$_SERVER['HTTP_HOST'] = $argv[3];
$_SERVER['SERVER_ADDR'] = '192.167.0.33';

$di = new BootstrapCli();
$di->setUrl();
if ($argv[1] !== 'install' && $argv[2] !== 'create') :
    $di->loadConfig();
    $di->loaderSystem();
    $di->database();
    $di->view();
elseif ($argv[1] === 'domain' && $argv[2] === 'create') :
    $di->loadConfig();
    require __DIR__ . '/Tasks/DomainTask.php';
else :
    DirectoryUtil::copy(
        __DIR__ . '/Resources/config/domain/example.com/',
        __DIR__ . '/../../../../config/domain/example.com/'
    );
    DirectoryUtil::copy(
        __DIR__ . '/Resources/config/account/example/',
        __DIR__ . '/../../../../config/account/example/'
    );
    $di->loadConfig();
    $di->setShared('request', new Request());
    $di->get('request');
    require __DIR__ . '/Tasks/InstallTask.php';
endif;

$console = new ConsoleApplication();
$console->setDI($di);
$console->attachListeners();

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
