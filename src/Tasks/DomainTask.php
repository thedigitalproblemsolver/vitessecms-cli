<?php declare(strict_types=1);

namespace VitesseCms\Cli\Tasks;

use Phalcon\Cli\Task;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Core\Utils\FileUtil;
use VitesseCms\User\Utils\PermissionUtils;

require_once __DIR__ . '/../../../core/src/Utils/DirectoryUtil.php';
require_once __DIR__ . '/../../../core/src/Utils/FileUtil.php';

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
        $scssDir = $this->rootDir . 'config/account/' . $params['account'].'/scss';

        DirectoryUtil::exists($this->rootDir . 'config/domain/' . $params['domain'], true);
        DirectoryUtil::exists($scssDir, true);
        DirectoryUtil::exists($this->rootDir . 'public_html/assets/'.$params['account'].'/js/cache', true);
        DirectoryUtil::exists($this->rootDir . 'public_html/assets/'.$params['account'].'/css/cache', true);

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

        $accountConfigDevIni = $this->rootDir . 'config/account/' . $params['account'].'/config_dev.ini';
        if(!FileUtil::exists($accountConfigDevIni)) :
            FileUtil::exists($accountConfigIni, true);
            $content = 'template = core
upload = '.$params['account'].'
https = false
ecommerce = false
languageShortDefault = en

[mongo]
database = '.$params['account'].'
ip = 192.167.0.22

[elasticsearch]
host = 192.167.0.55

[beanstalk]
host = 192.167.0.44
port = 11300
            ';
            file_put_contents($accountConfigDevIni, $content);
        endif;

        DirectoryUtil::copy(
            $this->rootDir.'vendor/vitessecms/install/src/Resources/files/scss',
            $scssDir
        );

        $permissionsFile = PermissionUtils::getAccessFileName();
        if(!FileUtil::exists($permissionsFile)) :
            $hash = gzdeflate(
                base64_encode(
                    serialize(PermissionUtils::getDefaults())
                )
            );
            file_put_contents(PermissionUtils::getAccessFileName(), $hash);
        endif;
    }
}
