<?php

declare(strict_types=1);

namespace User\Plugin;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Model\Table\AuthTable;

class AuthPluginFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authPlugin = new AuthPlugin();

        if ($container->has(AuthenticationService::class)) {
            $authPlugin->setAuthenticationService(
                $container->get(AuthenticationService::class)
            );
        } elseif ($container->has(AuthenticationServiceInterface::class)) {
            $authPlugin->setAuthenticationService(
                $container->get(AuthenticationServiceInterface::class)
            );
        }

        if ($container->has(AuthTable::class)) {
            $authPlugin->setAuthTable(
                $container->get(AuthTable::class)
            );
        }

        return $authPlugin;
    }
}
