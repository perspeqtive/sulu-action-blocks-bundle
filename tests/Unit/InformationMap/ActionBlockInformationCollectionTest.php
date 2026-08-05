<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationCollection;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;
use function json_encode;

final class ActionBlockInformationCollectionTest extends TestCase
{
    private ActionBlockInformationCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new ActionBlockInformationCollection();
    }

    public function testCollectionIsEmptyInitially(): void
    {
        self::assertTrue($this->collection->isEmpty());
        self::assertNull($this->collection->first());
    }

    public function testAddMakesInformationAvailableThroughIteratorAndFirst(): void
    {
        $information = new ActionBlockInformation('first-block', 'First', 'first');
        $secondInformation = new ActionBlockInformation('second-block', 'Second', 'second');

        $this->collection->add($information);
        $this->collection->add($secondInformation);

        self::assertFalse($this->collection->isEmpty());
        $firstInformation = $this->collection->first();

        self::assertSame($information, $firstInformation);
        self::assertSame([$information, $secondInformation], iterator_to_array($this->collection));
    }

    public function testSortSortsByTitle(): void
    {
        $information = new ActionBlockInformation('a', 'Z', 'first');
        $secondInformation = new ActionBlockInformation('b', 'U', 'second');
        $thirdInformation = new ActionBlockInformation('ä', 'u', 'second');

        $this->collection->add($information);
        $this->collection->add($secondInformation);
        $this->collection->add($thirdInformation);

        $this->collection->sort();

        $sortedCollection = iterator_to_array($this->collection);

        self::assertSame($secondInformation, $sortedCollection[0]);
        self::assertSame($thirdInformation, $sortedCollection[1]);
        self::assertSame($information, $sortedCollection[2]);
    }

    public function testFindByBlockNameReturnsMatchingInformation(): void
    {
        $information = new ActionBlockInformation('target-block', 'Target', 'target');
        $this->collection->add($information);

        self::assertSame($information, $this->collection->findByBlockName('target-block'));
        self::assertNull($this->collection->findByBlockName('unknown-block'));
    }

    public function testJsonSerializeReturnsInformation(): void
    {
        $information = new ActionBlockInformation('target-block', 'Target', 'target', true);
        $this->collection->add($information);

        self::assertSame('[{"blockName":"target-block","title":"Target","identifier":"target","needsGeneration":true}]', json_encode($this->collection));
    }

    public function testFromArrayCreatesCollectionFromInformationData(): void
    {
        $collection = ActionBlockInformationCollection::fromArray([
            [
                'blockName' => 'first-block',
                'title' => 'First',
                'identifier' => 'first',
            ],
            [
                'blockName' => 'second-block',
                'title' => 'Second',
                'identifier' => 'second',
                'needsGeneration' => true,
            ],
        ]);

        self::assertFalse($collection->isEmpty());
        $firstInformation = $collection->first();
        $secondInformation = $collection->findByBlockName('second-block');

        self::assertNotNull($firstInformation);
        self::assertSame('first-block', $firstInformation->blockName);
        self::assertFalse($firstInformation->needsGeneration);
        self::assertNotNull($secondInformation);
        self::assertSame('second', $secondInformation->identifier);
        self::assertTrue($secondInformation->needsGeneration);
    }
}
