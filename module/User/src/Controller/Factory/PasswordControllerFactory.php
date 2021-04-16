<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\PasswordController;
use User\Model\Table\AuthTable;
use User\Model\Table\ForgotTable;

class PasswordControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PasswordController(
            $container->get(AuthTable::class),
            $container->get(ForgotTable::class)
        );
    }
}
