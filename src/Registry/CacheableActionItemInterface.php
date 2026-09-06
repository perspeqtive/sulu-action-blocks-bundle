<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Registry;

interface CacheableActionItemInterface
{
    /**
     * Shared cache lifetime of the action's output in seconds. Values below 1 disable caching.
     */
    public function getCacheTtl(): int;
}
