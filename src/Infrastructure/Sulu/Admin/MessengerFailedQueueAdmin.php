<?php

declare(strict_types=1);

namespace Tailr\SuluMessengerFailedQueueBundle\Infrastructure\Sulu\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ListItemAction;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Tailr\SuluMessengerFailedQueueBundle\Presentation\Controller\Admin\ListController;

class MessengerFailedQueueAdmin extends Admin
{
    final public const SECURITY_CONTEXT = 'failed_queue';
    final public const LIST_KEY = 'failed_queue_list';
    private const LIST_VIEW = 'tailr.view_failed_queue_list';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if (!$this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            return;
        }

        $navigationItem = new NavigationItem('Failed Queue');
        $navigationItem->setPosition(10);
        $navigationItem->setView(self::LIST_VIEW);
        $navigationItemCollection->get(Admin::SETTINGS_NAVIGATION_ITEM)->addChild($navigationItem);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        if (!$this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            return;
        }

        $listToolbarActions = [];

        if ($this->securityChecker->hasPermission(self::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        // Configure List View
        $listView = $this->viewBuilderFactory->createListViewBuilder(self::LIST_VIEW, '/messenger-failed-queue')
            ->setResourceKey(ListController::RESOURCE_KEY)
            ->setListKey(self::LIST_KEY)
            ->setTitle('Failed Queue')
            ->addListAdapters(['table'])
            ->addItemActions([new ListItemAction('failed_queue.view_retry')])
            ->addToolbarActions($listToolbarActions);
        $viewCollection->add($listView);
    }

    public function getSecurityContexts(): array
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                Admin::SETTINGS_NAVIGATION_ITEM => [
                    self::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
