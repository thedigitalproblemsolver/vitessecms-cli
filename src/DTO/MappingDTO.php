<?php declare(strict_types=1);

namespace VitesseCms\Cli\DTO;

use \ArrayIterator;

final class MappingDTO implements MappingDTOInterface
{
    /**
     * @var ArrayIterator
     */
    private $iterator;

    /**
     * @var string
     */
    private $vendorDir;

    /**
     * @var string
     */
    private $publicHtmlDir;

    public function __construct( ArrayIterator $iterator, string $vendorDir, string $publicHtmlDir)
    {
        $this->iterator = $iterator;
        $this->vendorDir = $vendorDir;
        $this->publicHtmlDir = $publicHtmlDir;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->iterator;
    }

    public function getVendorDir(): string
    {
        return $this->vendorDir;
    }

    public function getPublicHtmlDir(): string
    {
        return $this->publicHtmlDir;
    }
}