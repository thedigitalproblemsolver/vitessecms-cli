<?php declare(strict_types=1);

namespace VitesseCms\Cli\Models;

class Mapping
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $target;

    public function __construct(string $source, string $target)
    {
        $this->source = $source;
        $this->target = $target;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getTarget(): string
    {
        return $this->target;
    }
}
