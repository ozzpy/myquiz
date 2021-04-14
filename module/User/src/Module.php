<?php

declare(strict_types=1);

namespace User;

use Laminas\Db\Adapter\Adapter;
use Laminas\Mvc\MvcEvent;
use User\Model\Table\AuthTable;
use User\Model\Table\ForgotTable;
use User\Model\Table\PrivilegesTable;
use User\Model\Table\ResourcesTable;
use User\Model\Table\RolesTable;
use User\Plugin\AuthPlugin;
use User\Plugin\AuthPluginFactory;
use User\Service\AccessTrait;
use User\View\Helper\AuthHelper;

class Module
{
    use AccessTrait;

    public function onBootstrap(MvcEvent $event) 
    {
        $app = $event->getApplication();
        $eventManager = $app->getEventManager();
        $eventManager->attach($event::EVENT_DISPATCH, [$this, 'getAccessPrivileges']);
    }

    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig() : array
    {
        return [
            'factories' => [
                AuthTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new AuthTable($dbAdapter);
                },
                ForgotTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new ForgotTable($dbAdapter);
                },
                PrivilegesTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new PrivilegesTable($dbAdapter);
                },
                ResourcesTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new ResourcesTable($dbAdapter);
                },
                RolesTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new RolesTable($dbAdapter);
                },
            ]
        ];
    }

    public function getControllerPluginConfig(): array
    {
        return [
            'aliases' => [
                'authPlugin' => AuthPlugin::class
            ],
            'factories' => [
                AuthPlugin::class => AuthPluginFactory::class
            ]
        ];
    }

    public function getViewHelperConfig(): array
    {
        return [
            'aliases' => [
                'authHelper' => AuthHelper::class
            ],
            'factories' => [
                AuthHelper::class => AuthPluginFactory::class
            ]
        ];
    }
}
