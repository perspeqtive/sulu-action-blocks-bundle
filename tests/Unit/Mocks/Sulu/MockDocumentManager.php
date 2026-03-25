<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Sulu;

use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\DocumentManager\Query\Query;

final class MockDocumentManager implements DocumentManagerInterface
{
    public mixed $documentToReturn = null;
    public ?string $receivedUuid = null;
    public ?string $receivedLocale = null;

    public function find($identifier, $locale = null, array $options = []): object
    {
        $this->receivedUuid = $identifier;
        $this->receivedLocale = $locale;
        if (null === $this->documentToReturn) {
            throw new \Exception('Document not found');
        }
        return (object) $this->documentToReturn;
    }

    public function persist($document, $locale = null, array $options = []): void {}
    public function remove($document): void {}
    public function move($document, $destId): void {}
    public function copy($document, $destPath): ?string { return null; }
    public function flush(): void {}
    public function clear(): void {}
    public function create($alias): object { return new \stdClass(); }
    public function findByPath($path, $locale = null, array $options = []): object { return new \stdClass(); }
    public function createQuery($query, $locale = null, array $options = []): Query { throw new \Exception('Not implemented'); }
    public function removeLocale($document, $locale): void {}
    public function copyLocale($document, $srcLocale, $destLocale): void {}
    public function reorder($document, $destId): void {}
    public function publish($document, $locale = null, array $options = []): void {}
    public function unpublish($document, $locale): void {}
    public function removeDraft($document, $locale): void {}
    public function restore($document, $locale, $version, array $options = []): void {}
    public function refresh($document): void {}
}
