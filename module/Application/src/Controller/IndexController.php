<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application\Controller;

use Application\Model\Table\QuizzesTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private $quizzesTable;

    public function __construct(QuizzesTable $quizzesTable)
    {
        $this->quizzesTable = $quizzesTable;
    }

    public function indexAction()
    {
        $paginator = $this->quizzesTable->fetchAllQuizzes(true);
        $page      = (int) $this->params()->fromQuery('page', 1);
        $page      = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel([
            'quizzes' => $paginator
        ]);
    }
}
