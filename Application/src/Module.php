<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Application\Form\Quiz\CreateForm;
use Application\Model\Table\AnswersTable;
use Application\Model\Table\CategoriesTable;
use Application\Model\Table\CommentsTable;
use Application\Model\Table\QuizzesTable;
use Application\Model\Table\TalliesTable;
use Application\View\Helper\CommentHelper;
use Application\View\Helper\CommentHelperFactory;
use Laminas\Db\Adapter\Adapter;
use Laminas\Mvc\MvcEvent;
use User\Service\AccessTrait;

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
                AnswersTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new AnswersTable($dbAdapter);
                },
                CategoriesTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new CategoriesTable($dbAdapter);
                },
                CommentsTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new CommentsTable($dbAdapter);
                },
                QuizzesTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new QuizzesTable($dbAdapter);
                },
                TalliesTable::class => function ($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new TalliesTable($dbAdapter);
                },
            ]
        ];
    }

    public function getFormElementConfig(): array
    {
        return [
            'factories' => [
                CreateForm::class => function ($sm) {
                    $categoriesTable = $sm->get(CategoriesTable::class);
                    return new CreateForm($categoriesTable);
                }
            ]
        ];
    }

    public function getViewHelperConfig(): array
    {
        return [
            'aliases' => [
                'commentHelper' => CommentHelper::class
            ],
            'factories' => [
                CommentHelper::class => CommentHelperFactory::class
            ]
        ];
    }
}
