<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\DependencyInjection\Compiler;

use ReflectionClass;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function class_exists;

class TrackActionClassFilesPass implements CompilerPassInterface
{
    public const ACTION_TAG = 'perspeqtive.sulu_action_block.action';

    public function process(ContainerBuilder $container): void
    {
        if ($container->isTrackingResources() === false) {
            return;
        }

        foreach ($container->findTaggedServiceIds(self::ACTION_TAG) as $serviceId => $tags) {
            $className = $container->getDefinition($serviceId)->getClass() ?? $serviceId;
            if (class_exists($className) === false) {
                continue;
            }

            $fileName = (new ReflectionClass($className))->getFileName();
            if ($fileName === false) {
                continue;
            }

            $container->addResource(new FileResource($fileName));
        }
    }
}
