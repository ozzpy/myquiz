<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Model\Table\AuthTable;
use User\Model\UrlModel;

class AdminController extends AbstractActionController
{
    private $authTable;

    public function __construct(AuthTable $authTable)
    {
        $this->authTable = $authTable;
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

        $paginator = $this->authTable->fetchAllAccounts(true);
        $page      = (int) $this->params()->fromQuery('page', 1);
        $page      = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel([
             'accounts' => $paginator
        ]);
    }
}
