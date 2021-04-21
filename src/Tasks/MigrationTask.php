<?php declare(strict_types=1);

namespace VitesseCms\Cli\Tasks;

use Phalcon\Cli\Task;
use VitesseCms\Cli\Services\TerminalService;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Install\Repositories\MigrationCollection;
use VitesseCms\Install\Repositories\MigrationRepository;
use VitesseCms\Install\Utils\MigrationUtil;

class MigrationTask extends Task
{
    public function upAction(): void
    {
        $this->getMigrationUtil()->executeUp();
    }

    public function rerunallAction(): void {
        $this->getMigrationUtil()->rerunAll();
    }

    protected function getMigrationUtil(): MigrationUtil {
        return (new MigrationUtil(
            $this->getDI()->getConfiguration(),
            new MigrationCollection(
                new DatagroupRepository(),
                new MigrationRepository(),
            ),
            new TerminalService()
        ));
    }
}
