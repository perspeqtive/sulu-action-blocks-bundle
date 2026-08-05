<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

use function count;
use function strnatcasecmp;
use function usort;

class ActionBlockInformationCollection implements IteratorAggregate, JsonSerializable
{
    /**
     * @var ActionBlockInformation[]
     */
    private array $information = [];

    public function add(ActionBlockInformation $information): void
    {
        $this->information[] = $information;
    }

    /**
     * @return ArrayIterator<ActionBlockInformation>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->information);
    }

    public function isEmpty(): bool
    {
        return count($this->information) === 0;
    }

    public function findByBlockName(string $actionBlockName): ?ActionBlockInformation
    {
        foreach ($this->information as $information) {
            if ($information->blockName === $actionBlockName) {
                return $information;
            }
        }

        return null;
    }

    public function first(): ?ActionBlockInformation
    {
        return $this->information[0] ?? null;
    }

    public function jsonSerialize(): array
    {
        return $this->information;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();
        foreach ($data as $information) {
            $collection->add(ActionBlockInformation::fromArray($information));
        }

        return $collection;
    }

    public function sort(): void
    {
        usort($this->information, function (ActionBlockInformation $a, ActionBlockInformation $b) {
            return strnatcasecmp($a->title, $b->title);
        });
    }
}
