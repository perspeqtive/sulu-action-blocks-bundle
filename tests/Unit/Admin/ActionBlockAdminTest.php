<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Admin;

use PERSPEQTIVE\SuluActionBlockBundle\Admin\ActionBlockAdmin;
use PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks\MockSecurityChecker;
use PHPUnit\Framework\TestCase;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactory;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;

class ActionBlockAdminTest extends TestCase
{
    private ViewBuilderFactory $viewBuilderFactory;
    private MockSecurityChecker $securityChecker;
    private ActionBlockAdmin $admin;

    protected function setUp(): void
    {
        $this->viewBuilderFactory = new ViewBuilderFactory();
        $this->securityChecker = new MockSecurityChecker();

        $this->admin = new ActionBlockAdmin(
            $this->viewBuilderFactory,
            $this->securityChecker,
        );
    }

    public function testConfigureNavigationItems(): void
    {
        $navigationItemCollection = new NavigationItemCollection();

        $this->admin->configureNavigationItems($navigationItemCollection);

        self::assertCount(1, $navigationItemCollection->all());
        self::assertEquals('Action Blocks', $navigationItemCollection->all()[0]->getLabel());
    }

    public function testConfigureViews(): void
    {
        $viewCollection = new ViewCollection();

        $this->admin->configureViews($viewCollection);

        self::assertCount(5, $viewCollection->all());
    }

    public function testGetSecurityContexts(): void
    {
        $contexts = $this->admin->getSecurityContexts();

        self::assertSame([
            'Sulu' => [
                'Action Blocks' => [
                    'sulu.action_blocks.action_blocks' => [
                        'view',
                        'add',
                        'edit',
                        'delete',
                    ],
                ],
            ],
        ], $contexts);
    }
}
