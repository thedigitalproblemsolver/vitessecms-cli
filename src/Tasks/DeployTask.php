<?php declare(strict_types=1);

namespace VitesseCms\Cli\Tasks;

use Phalcon\Cli\Task;
use Phalcon\Config\Adapter\Json;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter\Crunched;
use VitesseCms\Cli\DTO\MappingDTO;
use VitesseCms\Cli\Models\Mapping;
use VitesseCms\Cli\Models\MappingIterator;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Core\Utils\FileUtil;

class DeployTask extends Task
{
    private string $coreAssetsDir;
    private string $publicHtmlDir;
    private string $vendorDir;
    private string $accountDir;
    private string $assetsDir;
    private string $vitesseCmsSrcDir;
    private array $accountMapping;

    public function initialize()
    {
        $this->publicHtmlDir = __DIR__ . '/../../../../../public_html/';
        $this->vendorDir = __DIR__ . '/../../../../';
        $this->accountDir = $this->getDI()->getConfiguration()->getAccountDir();
        $this->assetsDir = $this->getDI()->getConfiguration()->getAssetsDir();
        $this->vitesseCmsSrcDir = $this->vendorDir . 'vitessecms/';
        $this->eventsManager = $this->getDI()->getEventsManager();
    }

    public function assetsAction(): void
    {
        $this->coreAssetsDir = $this->publicHtmlDir . 'assets/default/';

        $this->accountMapping = [];
        if (is_file($this->accountDir . 'Deploy/FileMapping.json')) :
            $this->accountMapping = (new Json($this->accountDir . 'Deploy/FileMapping.json'))->toArray();
        endif;
        $this->parseMapping($this->getJSMapping());
        $this->parseMapping($this->getImageMapping());
        $this->parseMapping($this->getCssMapping());

        $this->buildCss();
        $this->buildAdminCss();
    }

    protected function parseMapping(MappingIterator $mappingIterator): void
    {
        while ($mappingIterator->valid()) :
            $mapping = $mappingIterator->current();
            if (substr_count($mapping->getSource(), '/*') === 1) :
                $dir = str_replace('/*', '/', $mapping->getSource());
                foreach (DirectoryUtil::getFilelist($dir) as $file) :
                    $this->copy($dir . $file, $mapping->getTarget() . $file);
                endforeach;
            else :
                $this->copy($mapping->getSource(), $mapping->getTarget());
            endif;
            $mappingIterator->next();
        endwhile;
    }

    protected function copy(string $source, string $target): void
    {
        if (is_file($source) && FileUtil::copy($source, $target)) :
            echo "\033[32m" . 'copied ' . $source . ' to ' . $target . PHP_EOL . "\e[0m";
        else :
            echo "\033[41m" . ' failed copying of ' . $source . ' to ' . $target . PHP_EOL . "\e[0m";
        endif;
    }

    /**
     * @TODO move all module implementation to different packages
     */
    protected function getJSMapping(): MappingIterator
    {
        $jsMapping = new MappingIterator([
            new Mapping(
                $this->vendorDir . 'vitessecms/filemanager/src/Resources/js/*',
                $this->publicHtmlDir . 'assets/default/js/'
            ),
            new Mapping(
                $this->vitesseCmsSrcDir . 'core/src/Resources/js/*',
                $this->publicHtmlDir . 'assets/default/js/'
            )
        ]);

        $mappingDTO = new MappingDTO($jsMapping, $this->vendorDir, $this->publicHtmlDir);
        $this->getEventsManager()->fire('Deploy:JSMapping', $mappingDTO);
        if (!empty($this->accountMapping['javascript'])):
            foreach ($this->accountMapping['javascript'] as $image) :
                $mappingDTO->iterator->add(new Mapping(
                    __DIR__ . '/../../vitessecms/' . $image['source'],
                    __DIR__ . '/../../vitessecms/' . $image['target']));
            endforeach;
        endif;

        return $mappingDTO->iterator;
    }

    protected function getImageMapping(): MappingIterator
    {
        $imageMapping = new MappingIterator([
            new Mapping(
                $this->vendorDir . 'itsjavi/bootstrap-colorpicker/dist/img/bootstrap-colorpicker/*',
                $this->publicHtmlDir . 'assets/default/images/'
            ),
            new Mapping(
                $this->vendorDir . 'components/flag-icon-css/flags/1x1/*',
                $this->publicHtmlDir . 'assets/default/images/flags/1x1/'
            ),
            new Mapping(
                $this->vendorDir . 'components/flag-icon-css/flags/4x3/*',
                $this->publicHtmlDir . 'assets/default/images/flags/4x3/'
            ),
        ]);

        if (!empty($this->accountMapping['images'])):
            foreach ($this->accountMapping['images'] as $image) :
                $imageMapping->add(new Mapping(
                    __DIR__ . '/../../vitessecms/' . $image['source'],
                    __DIR__ . '/../../vitessecms/' . $image['target']));
            endforeach;
        endif;

        return $imageMapping;
    }

    protected function getCssMapping(): MappingIterator
    {
        $mappingDTO = new MappingDTO(new MappingIterator([]), $this->vendorDir, $this->publicHtmlDir);
        $this->eventsManager->fire('Deploy:CssMapping', $mappingDTO);

        if (!empty($this->accountMapping['css'])):
            foreach ($this->accountMapping['css'] as $image) :
                $mappingDTO->iterator->add(new Mapping(
                    __DIR__ . '/../../vitessecms/' . $image['source'],
                    __DIR__ . '/../../vitessecms/' . $image['target']));
            endforeach;
        endif;

        return $mappingDTO->iterator;
    }

    protected function buildCss(): void
    {
        $scssCompiler = new Compiler();
        $scssCompiler->addImportPath($this->accountDir . 'scss/');
        $scssCompiler->setFormatter(Crunched::class);
        $scssCompiled = $scssCompiler->compile(
            file_get_contents($this->accountDir . 'scss/site.scss')
        );

        file_put_contents($this->assetsDir . 'css/site.css', $scssCompiled);
    }

    protected function buildAdminCss(): void
    {
        $scssCompiler = new Compiler();
        $scssCompiler->addImportPath($this->vitesseCmsSrcDir . 'core/src/scss/');
        $scssCompiler->setFormatter(Crunched::class);
        $scssCompiled = $scssCompiler->compile(
            file_get_contents($this->vitesseCmsSrcDir . 'core/src/scss/admin.scss')
        );

        file_put_contents($this->coreAssetsDir . 'css/admin.css', $scssCompiled);
    }

    public function cssAction(): void
    {
        $this->buildCss();
    }
}
