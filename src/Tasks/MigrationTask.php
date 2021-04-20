<?php declare(strict_types=1);

namespace VitesseCms\Cli\Tasks;

use Phalcon\Cli\Task;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Install\Repositories\MigrationCollection;
use VitesseCms\Install\Utils\MigrationUtil;

class MigrationTask extends Task
{
    public function upAction(array $params): void
    {
        MigrationUtil::executeUp(
            $this->getDI()->getConfiguration(),
            new MigrationCollection(
                new DatagroupRepository()
            )
        );
    }
}
