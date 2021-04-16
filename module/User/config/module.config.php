<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace User;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'forgot' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/forgot',
                    'defaults' => [
                        'controller' => Controller\PasswordController::class,
                        'action'     => 'forgot',
                    ],
                ],
            ],
            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'profile' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/profile[/:id[/:username[/:action]]]',
                    'constraints' => [
                        'id'       => '[0-9]+',
                        'username' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'signup' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/signup',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'create',
                    ],
                ],
            ],
            'login' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/login[/:returnUrl]',
                    'constraints' => [
                        'returnUrl' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\LoginController::class => Controller\Factory\LoginControllerFactory::class,
            Controller\LogoutController::class => InvokableFactory::class,
            Controller\PasswordController::class => Controller\Factory\PasswordControllerFactory::class,
            Controller\ProfileController::class => Controller\Factory\ProfileControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            /** admin template map */
            'admin/index' => __DIR__ . '/../view/user/admin/index.phtml',
            /** auth template map */
            'auth/create' => __DIR__ . '/../view/user/auth/create.phtml',
            /** login template map */
            'login/index' => __DIR__ . '/../view/user/auth/login.phtml',
            /** password template map */
            'password/forgot' => __DIR__ . '/../view/user/auth/forgot.phtml',
            'password/reset' => __DIR__ . '/../view/user/auth/reset.phtml',
        ],
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
    ],
];
