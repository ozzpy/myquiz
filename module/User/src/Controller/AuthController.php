<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Form\Auth\CreateForm;
use User\Model\Table\AuthTable;

class AuthController extends AbstractActionController
{
    private $authTable;

    public function __construct(AuthTable $authTable)
    {
        $this->authTable = $authTable;
    }

    public function createAction()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $createForm = new CreateForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $createForm->setInputFilter($this->authTable->getCreateFormFilter());
            $createForm->setData($formData);

            if ($createForm->isValid()) {
                try {
                    $data = $createForm->getData();
                    $this->authTable->insertAccount($data);

                    $this->flashMessenger()->addSuccessMessage('Account successfully created. You can now login');
                    return $this->redirect()->toRoute('login');
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }
        
        return new ViewModel(['form' => $createForm]);
    }
}
