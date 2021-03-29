<?php declare(strict_types=1);

namespace VitesseCms\Cli\Tasks;

use Phalcon\Cli\Task;
use VitesseCms\Core\Utils\DirectoryUtil;

require_once __DIR__ . '/../../../core/src/Utils/DirectoryUtil.php';

class InstallTask extends Task
{
    /** @var String */
    protected $rootDir;

    public function initialize()
    {
        $this->rootDir = __DIR__ . '/../../../../../';
    }

    public function finishAction(array $params): void
    {
        DirectoryUtil::copy(
            $this->rootDir.'vendor/vitessecms/install/src/Resources/files/public_html',
            $this->rootDir.'public_html'
        );
    }
}
