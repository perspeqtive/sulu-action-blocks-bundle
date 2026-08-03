<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

readonly class ActionBlockInformation
{
    public function __construct(
        public string $blockName,
        public string $title,
        public string $identifier,
        public bool $needsGeneration = false,
    ) {
    }

    public static function fromArray(mixed $information): self
    {
        return new self(
            $information['blockName'],
            $information['title'],
            $information['identifier'],
            $information['needsGeneration'] ?? false,
        );
    }
}
