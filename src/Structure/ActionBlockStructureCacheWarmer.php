<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure;

use RuntimeException;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function sprintf;

readonly class ActionBlockStructureCacheWarmer implements CacheWarmerInterface
{
    public const RELATIVE_TEMPLATE_PATH = '/perspeqtive_sulu_action_blocks/blocks/action-block.xml';

    public function __construct(private ActionBlockTemplateGenerator $templateGenerator)
    {
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $templatePath = $cacheDir . self::RELATIVE_TEMPLATE_PATH;

        $templateDirectory = dirname($templatePath);
        if (is_dir($templateDirectory) === false && @mkdir($templateDirectory, 0777, true) === false) {
            throw new RuntimeException(sprintf('Could not create directory "%s".', $templateDirectory));
        }

        if (@file_put_contents($templatePath, $this->templateGenerator->generate()) === false) {
            throw new RuntimeException(sprintf('Could not write generated action block template "%s".', $templatePath));
        }

        return [];
    }

    public function isOptional(): bool
    {
        return false;
    }
}
