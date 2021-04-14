<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\Table\CommentsTable;
use Application\Model\Table\QuizzesTable;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Model\UrlModel;

class AdminController extends AbstractActionController
{
    private $commentsTable;
    private $quizzesTable;

    public function __construct(
        CommentsTable $commentsTable,
        QuizzesTable $quizzesTable
    ) {
        $this->commentsTable = $commentsTable;
        $this->quizzesTable = $quizzesTable;
    }

    public function indexAction()
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute(
                'login',
                [
                    'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
                ]
            );
        }

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
