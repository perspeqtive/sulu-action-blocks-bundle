<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\DependencyInjection;

use PERSPEQTIVE\SuluActionBlocksBundle\Controller\ActionBlockFragmentController;
use PERSPEQTIVE\SuluActionBlocksBundle\Fragment\EsiActionBlockFragmentRenderer;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionNamedType;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

use function class_exists;
use function dirname;
use function interface_exists;
use function is_string;
use function ltrim;
use function sprintf;
use function str_starts_with;

final class ServicesConfigurationTest extends TestCase
{
    private const NAMESPACE_PREFIX = 'PERSPEQTIVE\\SuluActionBlocksBundle\\';

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $configPath = dirname(__DIR__, 3) . '/config';

        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator($configPath));
        $loader->load('services.yaml');
    }

    public function testEveryDefinitionReferencesAnExistingClass(): void
    {
        foreach ($this->getBundleDefinitions() as $id => $definition) {
            self::assertTrue(
                class_exists((string) $definition->getClass()),
                sprintf('Service "%s" references an unknown class "%s".', $id, (string) $definition->getClass()),
            );
        }
    }

    public function testEveryNamedArgumentMatchesAConstructorParameter(): void
    {
        foreach ($this->getBundleDefinitions() as $id => $definition) {
            $parameterNames = $this->getConstructorParameterNames((string) $definition->getClass());

            foreach ($definition->getArguments() as $argumentName => $argumentValue) {
                self::assertContains(
                    ltrim((string) $argumentName, '$'),
                    $parameterNames,
                    sprintf('Service "%s" configures unknown argument "%s".', $id, (string) $argumentName),
                );
            }
        }
    }

    public function testEveryRequiredConstructorParameterIsConfigured(): void
    {
        foreach ($this->getBundleDefinitions() as $id => $definition) {
            if ($definition->getFactory() !== null) {
                continue;
            }

            $configuredArguments = [];
            foreach ($definition->getArguments() as $argumentName => $argumentValue) {
                $configuredArguments[] = ltrim((string) $argumentName, '$');
            }

            foreach ($this->getRequiredConstructorParameterNames((string) $definition->getClass()) as $parameterName) {
                self::assertContains(
                    $parameterName,
                    $configuredArguments,
                    sprintf('Service "%s" is missing the required argument "$%s".', $id, $parameterName),
                );
            }
        }
    }

    public function testExecutorIsWiredWithTheEsiFragmentRenderer(): void
    {
        $arguments = $this->container
            ->getDefinition('PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutor')
            ->getArguments();

        self::assertSame(
            'PERSPEQTIVE\SuluActionBlocksBundle\Fragment\EsiActionBlockFragmentRenderer',
            (string) $arguments['$fragmentRenderer'],
        );
    }

    public function testFragmentControllerIsPubliclyRegisteredWithControllerArguments(): void
    {
        $definition = $this->container->getDefinition(
            'PERSPEQTIVE\SuluActionBlocksBundle\Controller\ActionBlockFragmentController',
        );

        self::assertTrue($definition->isPublic());
        self::assertArrayHasKey('controller.service_arguments', $definition->getTags());
    }

    public function testFragmentRouteTargetsTheFragmentController(): void
    {
        $routes = Yaml::parseFile(dirname(__DIR__, 3) . '/config/routes.yaml');

        self::assertArrayHasKey(EsiActionBlockFragmentRenderer::ROUTE, $routes);

        $route = $routes[EsiActionBlockFragmentRenderer::ROUTE];

        self::assertSame(ActionBlockFragmentController::class . '::index', $route['controller']);
        self::assertSame(['GET'], $route['methods']);
        self::assertTrue($this->container->has(ActionBlockFragmentController::class));
    }

    /**
     * @return array<string, Definition>
     */
    private function getBundleDefinitions(): array
    {
        $definitions = [];
        foreach ($this->container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();
            if (is_string($class) === false || str_starts_with($class, self::NAMESPACE_PREFIX) === false) {
                continue;
            }
            $definitions[$id] = $definition;
        }

        self::assertNotEmpty($definitions);

        return $definitions;
    }

    /**
     * @return string[]
     */
    private function getConstructorParameterNames(string $class): array
    {
        $constructor = (new ReflectionClass($class))->getConstructor();
        if ($constructor === null) {
            return [];
        }

        $names = [];
        foreach ($constructor->getParameters() as $parameter) {
            $names[] = $parameter->getName();
        }

        return $names;
    }

    /**
     * @return string[]
     */
    private function getRequiredConstructorParameterNames(string $class): array
    {
        $constructor = (new ReflectionClass($class))->getConstructor();
        if ($constructor === null) {
            return [];
        }

        $names = [];
        foreach ($constructor->getParameters() as $parameter) {
            if ($parameter->isDefaultValueAvailable() === true) {
                continue;
            }
            $names[] = $parameter->getName();
        }

        return $names;
    }
}
