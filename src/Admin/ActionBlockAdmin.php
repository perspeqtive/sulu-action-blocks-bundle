<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Admin;

use PERSPEQTIVE\SuluActionBlockBundle\Entity\ActionBlock;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class ActionBlockAdmin extends Admin
{
    public const SECURITY_CONTEXT = 'sulu.action_blocks.action_blocks';

    public const LIST_VIEW = 'sulu_action_block.list';
    public const ADD_FORM_VIEW = 'sulu_action_block.add_form';
    public const EDIT_FORM_VIEW = 'sulu_action_block.edit_form';
    public const EDIT_FORM_DETAILS_VIEW = 'sulu_action_block.edit_form.details';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(static::SECURITY_CONTEXT, PermissionTypes::VIEW) === false) {
            return;
        }
        $navigationItem = new NavigationItem('sulu_action_block.action_blocks');
        $navigationItem->setLabel('Action Blocks');
        $navigationItem->setView(static::LIST_VIEW);
        $navigationItem->setIcon('su-process');
        $navigationItem->setPosition(40);

        $navigationItemCollection->add($navigationItem);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $viewCollection->add(
            $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, '/action-blocks')
                ->setResourceKey(ActionBlock::RESOURCE_KEY)
                ->setListKey('action_block')
                ->setTitle('Action Blocks')
                ->addListAdapters(['table'])
                ->setAddView(static::ADD_FORM_VIEW)
                ->setEditView(static::EDIT_FORM_VIEW)
                ->addToolbarActions(['sulu_admin.add', 'sulu_admin.delete']),
        );

        $viewCollection->add(
            $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, '/action-blocks/add')
                ->setResourceKey(ActionBlock::RESOURCE_KEY)
                ->setBackView(static::LIST_VIEW),
        );

        $viewCollection->add(
            $this->viewBuilderFactory->createFormViewBuilder(static::ADD_FORM_VIEW . '.details', '/details')
                ->setResourceKey(ActionBlock::RESOURCE_KEY)
                ->setFormKey('action_block_details')
                ->setTabTitle('sulu_admin.details')
                ->setEditView(static::EDIT_FORM_VIEW)
                ->addToolbarActions(['sulu_admin.save'])
                ->setParent(static::ADD_FORM_VIEW),
        );

        $viewCollection->add(
            $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, '/action-blocks/:id')
                ->setResourceKey(ActionBlock::RESOURCE_KEY)
                ->setBackView(static::LIST_VIEW)
                ->setTitleProperty('title'),
        );

        $viewCollection->add(
            $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_FORM_DETAILS_VIEW, '/details')
                ->setResourceKey(ActionBlock::RESOURCE_KEY)
                ->setFormKey('action_block_details')
                ->setTabTitle('sulu_admin.details')
                ->addToolbarActions(['sulu_admin.save', 'sulu_admin.delete'])
                ->setParent(static::EDIT_FORM_VIEW),
        );
    }

    public function getSecurityContexts(): array
    {
        return [
            'Sulu' => [
                'Action Blocks' => [
                    static::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
