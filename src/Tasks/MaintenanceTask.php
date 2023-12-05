<?php

declare(strict_types=1);

namespace VitesseCms\Cli\Tasks;

use Phalcon\Cli\Task;
use stdClass;
use VitesseCms\Content\Enum\ItemEnum;
use VitesseCms\Content\Models\Item;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Content\Utils\SeoUtil;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Datafield\Enum\DatafieldEnum;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Enums\DatagroupEnum;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Language\Enums\LanguageEnum;
use VitesseCms\Language\Repositories\LanguageRepository;

class MaintenanceTask extends Task
{
    private DatagroupRepository $datagroupRepository;
    private DatafieldRepository $datafieldRepository;
    private ItemRepository $itemRepository;
    private LanguageRepository $languageRepository;

    public function rebuildSlugsAction(): void
    {
        $this->datagroupRepository = $this->eventsManager->fire(DatagroupEnum::GET_REPOSITORY->value, new stdClass());
        $this->datafieldRepository = $this->eventsManager->fire(DatafieldEnum::GET_REPOSITORY->value, new stdClass());
        $this->itemRepository = $this->eventsManager->fire(ItemEnum::GET_REPOSITORY, new stdClass());
        $this->languageRepository = $this->eventsManager->fire(LanguageEnum::GET_REPOSITORY->value, new stdClass());

        $items = $this->itemRepository->findAll(
            new FindValueIterator([new FindValue('parentId', null)]),
            false,
            20000
        );
        while ($items->valid()) {
            $this->handleChildren($items->current());
            $items->next();
        }
    }

    private function handleChildren(Item $item): void
    {
        SeoUtil::setSlugsOnItem(
            $item,
            $this->datagroupRepository,
            $this->datafieldRepository,
            $this->itemRepository,
            $this->languageRepository,
            $this->datagroupRepository->getById($item->getDatagroup())
        )->save();

        if ($item->hasChildren()) {
            $children = $this->itemRepository->findAll(
                new FindValueIterator([new FindValue('parentId', (string)$item->getId())]),
                false,
                20000
            );
            while ($children->valid()) {
                $this->handleChildren($children->current());
                $children->next();
            }
        }
    }
}