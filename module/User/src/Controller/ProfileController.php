<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Model\Table\AuthTable;

class ProfileController extends AbstractActionController
{
    private $authTable;

    public function __construct(AuthTable $authTable)
    {
        $this->authTable = $authTable;
    }

    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!is_numeric($id) || !$this->authTable->fetchAccountByAuthId((int)$id)) {
            return $this->notFoundAction();
        }

        return new ViewModel(['account' => $this->authTable->fetchAccountByAuthId((int) $id)]);
    }
}
