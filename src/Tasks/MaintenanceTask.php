<?php declare(strict_types=1);

namespace VitesseCms\Cli\Tasks;

use Phalcon\Cli\Task;
use VitesseCms\Content\Models\Item;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Content\Utils\SeoUtil;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Language\Repositories\LanguageRepository;

class MaintenanceTask extends Task
{
    /**
     * @var DatagroupRepository
     */
    private $datagroupRepository;

    /**
     * @var DatafieldRepository
     */
    private $datafieldRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    public function rebuildSlugsAction(): void
    {
        $this->datagroupRepository = new DatagroupRepository();
        $this->datafieldRepository = new DatafieldRepository();
        $this->itemRepository = new ItemRepository();
        $this->languageRepository = new LanguageRepository();

        $items = (new ItemRepository())->findAll(new FindValueIterator([new FindValue('parentId',null)]), false, 20000);
        while ($items->valid() ):
            $item = $items->current();
            $this->handleChildren($item);
            $items->next();
        endwhile;
    }

    private function handleChildren(Item $item): void
    {
        SeoUtil::setSlugsOnItem(
            $item,
            $this->datagroupRepository,
            $this->datafieldRepository,
            $this->itemRepository,
            $this->languageRepository,
            $this->eventsManager
        )->save();

        if($item->hasChildren()) {
            $children = (new ItemRepository())->findAll(new FindValueIterator([new FindValue('parentId',(string)$item->getId())]), false, 20000);
            while ($children->valid() ):
                $child = $children->current();
                $this->handleChildren($child);
                $children->next();
            endwhile;
        }
    }
}