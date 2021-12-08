<?php declare(strict_types=1);

namespace VitesseCms\Cli\DTO;

use \ArrayIterator;

interface MappingDTOInterface
{
    public function getIterator(): ArrayIterator;

    public function getVendorDir(): string;

    public function getPublicHtmlDir(): string;
}