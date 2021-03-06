<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\QuizController;
use Application\Form\Quiz\CreateForm;
use Application\Model\Table\AnswersTable;
use Application\Model\Table\QuizzesTable;
use Application\Model\Table\TalliesTable;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class QuizControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        $formManager = $container->get('FormElementManager');
        return new QuizController(
            $container->get(AnswersTable::class),
            $formManager->get(CreateForm::class),
            $container->get(QuizzesTable::class),
            $container->get(TalliesTable::class)
        );
    }
}
