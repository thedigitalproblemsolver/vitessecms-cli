<?php declare(strict_types=1);

namespace VitesseCms\Cli\DTO;

use VitesseCms\Cli\Models\MappingIterator;

final class MappingDTO
{
    public function __construct(public MappingIterator $iterator, public readonly string $vendorDir, public readonly string $publicHtmlDir)
    {
    }
}