<?php declare(strict_types=1);

namespace VitesseCms\Cli;

use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\http\Request;
use Phalcon\Loader;
use VitesseCms\Configuration\Utils\AccountConfigUtil;
use VitesseCms\Configuration\Utils\DomainConfigUtil;
use VitesseCms\Core\Services\ConfigService;
use VitesseCms\Core\Services\UrlService;
use VitesseCms\Core\Utils\BootstrapUtil;
use VitesseCms\Core\Utils\SystemUtil;

class BootstrapCli extends Cli
{
    /**
     * @var string
     */
    protected $vitessecmsCoreDir;

    public function __construct()
    {
        parent::__construct();

        $this->vitessecmsCoreDir = str_replace('cli', 'core', __DIR__) . '/';
    }

    public function loaderSystem(): Loader
    {
        $loader = new Loader();
        $loader->registerDirs([
            $this->vitessecmsCoreDir . 'Helpers/',
            $this->vitessecmsCoreDir . 'Utils/'
        ])->register();

        $loader->registerNamespaces(
            [
                'VitesseCms\\Core\\Helpers' => $this->vitessecmsCoreDir . 'Helpers/',
                'VitesseCms\\Core\\Utils' => $this->vitessecmsCoreDir . 'Utils/',
            ]
        );

        $loader = BootstrapUtil::addModulesToLoader(
            $loader,
            SystemUtil::getModules($this->getConfiguration()),
            $this->getConfiguration()->getAccount()
        );

        return $loader;
    }

    public function getConfiguration(): ConfigService
    {
        return $this->get('configuration');
    }

    public function loadConfig(): BootstrapCli
    {
        $domainConfig = new DomainConfigUtil(__DIR__ . '/../../../../');
        $domainConfig->merge(new AccountConfigUtil($domainConfig->get('account')));
        $domainConfig->setDirectories();
        $domainConfig->setTemplate();

        $this->setShared('config', $domainConfig);
        $this->setShared('configuration', new ConfigService($domainConfig, $this->get('url')));

        return $this;
    }

    public function setUrl(): BootstrapCli
    {
        $this->setShared('url', new UrlService(new Request()));

        return $this;
    }
}
