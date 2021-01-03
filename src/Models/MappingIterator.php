<?php declare(strict_types=1);

namespace VitesseCms\Cli\Models;

class MappingIterator extends \ArrayIterator
{
    public function __construct(array $mappings)
    {
        parent::__construct($mappings);
    }

    public function current(): Mapping
    {
        return parent::current();
    }

    public function add(Mapping $value): void
    {
        $this->append($value);
    }
}
