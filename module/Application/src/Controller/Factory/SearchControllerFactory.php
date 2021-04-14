<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\SearchController;
use Application\Model\Table\QuizzesTable;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SearchControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SearchController(
            $container->get(QuizzesTable::class)
        );
    }
}
