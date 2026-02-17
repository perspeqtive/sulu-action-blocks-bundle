<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle;

use PERSPEQTIVE\SuluActionBlockBundle\Registry\ServiceActionItemInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function dirname;
use function glob;

class SuluActionBlockBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/../config/services.yaml');
        $this->configureAutoconfigurationInterface($builder);
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->setParameter('perspeqtive_sulu_action_block_bundle_path', dirname(__DIR__));

        foreach (glob(__DIR__ . '/../config/packages/*.yaml') as $file) {
            $container->import($file);
        }
    }

    protected function configureAutoconfigurationInterface(ContainerBuilder $builder): void
    {
        $builder
            ->registerForAutoconfiguration(ServiceActionItemInterface::class)
            ->addTag('perspeqtive.sulu_action_block.action');
    }
}
