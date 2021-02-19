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

class BoostrapCli extends Cli
{
    /**
     * @var string
     */
    protected $vitessecmsCoreDir;

    public function __construct()
    {
        parent::__construct();

        $this->vitessecmsCoreDir = str_replace('cli', 'vitessecms', __DIR__) . '/';
    }

    public function loaderSystem(): Loader
    {

        $loader = new Loader();
        $loader->registerDirs([
            $this->vitessecmsCoreDir . 'core/helpers/',
            $this->vitessecmsCoreDir . 'core/Utils/'
        ])->register();
        $loader->registerNamespaces(
            [
                'VitesseCms\\Core\\Helpers' => $this->vitessecmsCoreDir . 'core/helpers/',
                'VitesseCms\\Core\\Utils' => $this->vitessecmsCoreDir . 'core/Utils/',
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

    public function loadConfig(): BoostrapCli
    {
        $domainConfig = new DomainConfigUtil();
        $domainConfig->merge(new AccountConfigUtil($domainConfig->get('account')));
        $domainConfig->setDirectories();
        $domainConfig->setTemplate();

        $this->setShared('config', $domainConfig);
        $this->setShared('configuration', new ConfigService($domainConfig, $this->get('url')));

        return $this;
    }

    public function setUrl(): BoostrapCli
    {
        $this->setShared('url', new UrlService(new Request()));

        return $this;
    }
}
