<?php declare(strict_types=1);

namespace VitesseCms\Cli\Tasks;

use Phalcon\Cli\Task;
use VitesseCms\Cli\Services\TerminalService;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Core\Utils\SystemUtil;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Install\Interfaces\MigrationInterface;
use VitesseCms\Install\Models\Migration;
use VitesseCms\Install\Repositories\MigrationRepository;

class MigrationTask extends Task
{
    public function upAction(): void
    {
        $terminalService = new TerminalService();
        $modules = SystemUtil::getModules($this->getDI()->getConfiguration());
        $files = [];
        foreach ($modules as $module => $modulePath):
            $files = array_merge($files, DirectoryUtil::getFilelist($modulePath.'/Migrations'));
        endforeach;
        $files = array_flip($files);
        ksort($files);

        foreach ($files as $fileName => $filePath):
            $name = str_replace(
                $this->getDI()->getConfiguration()->getRootDir().'../',
                '',
                $filePath
            );
            if ((new MigrationRepository())->findFirst(new FindValueIterator([new FindValue('name', $name)])) === null):
                $terminalService->printHeader('Started ' . $name);
                require_once $filePath;
                /** @var MigrationInterface $className */
                $className = SystemUtil::createNamespaceFromPath($filePath);
                $result = (new $className())->up($this->getDI()->getConfiguration(), $terminalService);
                if ($result):
                    (new Migration())->setName($name)->setPublished(true)->save();
                endif;
                $terminalService->printHeader('Finished ' . $name);
            endif;
        endforeach;
    }

    public function rerunallAction(): void {
        $terminalService = new TerminalService();
        $terminalService->printHeader('Started rerun of all Migrations');
        $terminalService->printHeader('Started deleting of all Migrations');

        $migrations = (new MigrationRepository())->findAll(null, false);
        while ($migrations->valid()):
            $name = $migrations->current()->getName();
            if (!$migrations->current()->delete()):
                $terminalService->printError('deleting migration "' . $name . '"');
            endif;
            $migrations->next();
        endwhile;

        $terminalService->printHeader('Finished deleting of all Migrations');
        $this->upAction();
        $terminalService->printHeader('Finished rerun of all Migrations');
    }
}
