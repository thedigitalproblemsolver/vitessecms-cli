<?php declare(strict_types=1);

namespace VitesseCms\Cli;

use Phalcon\Cli\Console as ConsoleApp;
use VitesseCms\Core\Utils\SystemUtil;

class ConsoleApplication extends ConsoleApp
{
    public function attachListeners(): ConsoleApplication
    {
        foreach (SystemUtil::getModules($this->configuration) as $path) :
            $listenerPath = $path . '/Listeners/CliListeners.php';
            if (is_file($listenerPath)) :
                SystemUtil::createNamespaceFromPath($listenerPath)::setListeners($this);
            endif;
        endforeach;

        return $this;
    }
}