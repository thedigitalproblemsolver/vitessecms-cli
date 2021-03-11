<?php declare(strict_types=1);

namespace VitesseCms\Cli\Tasks;

use Phalcon\Cli\Task;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Core\Utils\FileUtil;

require __DIR__ . '/../../../core/src/Utils/DirectoryUtil.php';
require __DIR__ . '/../../../core/src/Utils/FileUtil.php';

class DomainTask extends Task
{
    /** @var String */
    protected $rootDir;

    public function initialize()
    {
        $this->rootDir = __DIR__ . '/../../../../../';
    }

    public function createAction(array $params): void
    {
        DirectoryUtil::exists($this->rootDir . 'config/domain/' . $params['domain'], true);
        DirectoryUtil::exists($this->rootDir . 'config/account/' . $params['account'], true);
        DirectoryUtil::exists($this->rootDir . 'public_html/uploads/'.$params['account'], true);
        DirectoryUtil::exists($this->rootDir . 'public_html/assets/'.$params['account'], true);
        DirectoryUtil::exists($this->rootDir . 'public_html/assets/'.$params['account'].'/js', true);
        DirectoryUtil::exists($this->rootDir . 'public_html/assets/'.$params['account'].'/css', true);

        $domainConfigIni = $this->rootDir . 'config/domain/' . $params['domain'].'/config.ini';
        if(!FileUtil::exists($domainConfigIni)) :
            FileUtil::exists($domainConfigIni, true);
            $content = 'account = '.$params['account'].'
            
[language]
locale=en-EN
short=en
            ';
            file_put_contents($domainConfigIni, $content);
        endif;

        $accountConfigIni = $this->rootDir . 'config/account/' . $params['account'].'/config.ini';
        if(!FileUtil::exists($accountConfigIni)) :
            FileUtil::exists($accountConfigIni, true);
            $content = 'template = core
upload = '.$params['account'].'
https = true
ecommerce = false
languageShortDefault = en

[mongo]
database = '.$params['account'].'

[elasticsearch]
host = 127.0.0.1

[beanstalk]
host = 127.0.0.1
port = 11300
            ';
            file_put_contents($accountConfigIni, $content);
        endif;
    }
}
