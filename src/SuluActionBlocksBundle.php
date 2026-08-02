<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle;

use PERSPEQTIVE\SuluActionBlocksBundle\DependencyInjection\Compiler\TrackActionClassFilesPass;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function dirname;
use function glob;

/**
 * @codeCoverageIgnore
 */
class SuluActionBlocksBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new TrackActionClassFilesPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }

    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $configurator->import(__DIR__ . '/../config/services.yaml');
        $this->configureAutoconfigurationInterface($container);
    }

    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $container->setParameter('perspeqtive_sulu_action_blocks_bundle_path', dirname(__DIR__));
        $container->setParameter('perspeqtive_sulu_action_blocks_cache_path', '/perspeqtive_sulu_action_blocks/blocks');
        $container->setParameter('perspeqtive_sulu_action_blocks_templates_path', '%perspeqtive_sulu_action_blocks_bundle_path%/config/templates/action-blocks');

        foreach (glob(__DIR__ . '/../config/packages/*.yaml') as $file) {
            $configurator->import($file);
        }
    }

    protected function configureAutoconfigurationInterface(ContainerBuilder $builder): void
    {
        $builder
            ->registerForAutoconfiguration(ServiceActionItemInterface::class)
            ->addTag(TrackActionClassFilesPass::ACTION_TAG);
    }
}
