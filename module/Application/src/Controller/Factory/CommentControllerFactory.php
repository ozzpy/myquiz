<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\CommentController;
use Application\Model\Table\CommentsTable;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new CommentController(
            $container->get(CommentsTable::class)
        );
    }
}
