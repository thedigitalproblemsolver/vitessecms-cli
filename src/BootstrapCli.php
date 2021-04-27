<?php declare(strict_types=1);

namespace VitesseCms\Cli;

use MongoDB\Client;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\http\Request;
use Phalcon\Loader;
use Phalcon\Mvc\Collection\Manager as CollectionManager;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Configuration\Utils\AccountConfigUtil;
use VitesseCms\Configuration\Utils\DomainConfigUtil;
use VitesseCms\Core\Services\UrlService;
use VitesseCms\Core\Utils\BootstrapUtil;
use VitesseCms\Core\Utils\DebugUtil;
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

        $file = 'config.ini';
        if (DebugUtil::isDev()) :
            $file = 'config_dev.ini';
        endif;
        $accountConfigFile = __DIR__ . '/../../../../config/account/' . $domainConfig->get('account') . '/' . $file;
        $domainConfig->merge(new AccountConfigUtil($accountConfigFile));
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

    public function database(): BootstrapCli
    {
        $configuration = $this->getConfiguration();

        $this->setShared(
            'mongo',
            (new Client($configuration->getMongoUri()))
                ->selectDatabase($configuration->getMongoDatabase())
        );
        $this->setShared('collectionManager', new CollectionManager());

        return $this;
    }
}
