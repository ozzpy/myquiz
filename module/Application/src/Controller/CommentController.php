<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Form\Comment\CreateForm;
use Application\Form\Comment\DeleteForm;
use Application\Form\Comment\EditForm;
use Application\Model\Table\CommentsTable;
use Application\Model\Table\QuizzesTable;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class CommentController extends AbstractActionController
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

    public function createAction()
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $createForm = new CreateForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $createForm->setInputFilter($this->commentsTable->getCreateFormFilter());
            $createForm->setData($formData);

            if ($createForm->isValid()) {

                try {
                    $data = $createForm->getData();
                    $this->commentsTable->insertComment($data);
                    $this->quizzesTable->updateComments((int) $data['quiz_id']);

                    return $this->redirect()->toRoute(
                        'quiz',
                        [
                            'action' => 'view',
                            'id' => $data['quiz_id']
                        ]
                    );
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return (new ViewModel())->setTerminal(true);
    }

    public function deleteAction()
    {
        $deleteForm = new DeleteForm();
        return new ViewModel(['form' => $deleteForm]);
    }

    public function editAction()
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $editForm = new EditForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $editForm->setInputFilter($this->commentsTable->getCreateFormFilter());
            $editForm->setData($formData);

            if ($editForm->isValid()) {
                try {
                    $data = $editForm->getData();
                    $this->commentsTable->updateComment($data);

                    $this->redirect()->toRoute('quiz', ['action' => 'view', 'id' => $data['quiz_id']]);
                } catch (\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return;
                }
            }
        }

        return (new ViewModel())->setTerminal(true);
    }
}
