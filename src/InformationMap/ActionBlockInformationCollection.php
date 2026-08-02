<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

use ArrayIterator;
use Traversable;

class ActionBlockInformationCollection implements \IteratorAggregate
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
        return $this->information[0];
    }
}