<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\DependencyInjection\Compiler;

use PERSPEQTIVE\SuluActionBlocksBundle\DependencyInjection\Compiler\TrackActionClassFilesPass;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

use function in_array;

class TrackActionClassFilesPassTest extends TestCase
{
    public function testTracksClassFileOfTaggedActionService(): void
    {
        $container = new ContainerBuilder();
        $definition = new Definition(MockServiceActionItem::class);
        $definition->addTag(TrackActionClassFilesPass::ACTION_TAG);
        $container->setDefinition(MockServiceActionItem::class, $definition);

        (new TrackActionClassFilesPass())->process($container);

        $expectedFileName = (new ReflectionClass(MockServiceActionItem::class))->getFileName();

        self::assertTrue($this->hasFileResource($container, (string) $expectedFileName));
    }

    public function testIgnoresServicesWithUnknownClass(): void
    {
        $container = new ContainerBuilder();
        $definition = new Definition('PERSPEQTIVE\NotExisting\ActionService');
        $definition->addTag(TrackActionClassFilesPass::ACTION_TAG);
        $container->setDefinition('not_existing_action', $definition);

        (new TrackActionClassFilesPass())->process($container);

        self::assertFalse($this->hasAnyFileResource($container));
    }

    public function testIgnoresServicesWithoutActionTag(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition(MockServiceActionItem::class, new Definition(MockServiceActionItem::class));

        (new TrackActionClassFilesPass())->process($container);

        self::assertFalse($this->hasAnyFileResource($container));
    }

    private function hasFileResource(ContainerBuilder $container, string $fileName): bool
    {
        $fileResources = [];
        foreach ($container->getResources() as $resource) {
            if ($resource instanceof FileResource === true) {
                $fileResources[] = (string) $resource;
            }
        }

        return in_array($fileName, $fileResources, true);
    }

    private function hasAnyFileResource(ContainerBuilder $container): bool
    {
        foreach ($container->getResources() as $resource) {
            if ($resource instanceof FileResource === true) {
                return true;
            }
        }

        return false;
    }
}
