<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\BcryptSha;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use PHPThumb\GD;
use RuntimeException;
use User\Form\Setting\DeleteForm;
use User\Form\Setting\EmailForm;
use User\Form\Setting\PasswordForm;
use User\Form\Setting\UploadForm;
use User\Form\Setting\UsernameForm;
use User\Model\Table\AuthTable;
use User\Model\UrlModel;

class SettingController extends AbstractActionController
{
    private $authTable;

    public function __construct(AuthTable $authTable)
    {
        $this->authTable = $authTable;
    }

    public function deleteAction()
    {
        $auth = new AuthenticationService();
        if(!$auth->hasIdentity()) {
            return $this->redirect()->toRoute(
                'login',
                [
                    'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
                ]
            );
        }

        $deleteForm = new DeleteForm();
        return new ViewModel(['form' => $deleteForm]);
    }

    public function emailAction()
    {
        $auth = new AuthenticationService();
        if(!$auth->hasIdentity()) {
            return $this->redirect()->toRoute(
                'login',
                [
                    'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
                ]
            );
        }

        $emailForm = new EmailForm();
        $request = $this->getRequest();

        if($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $emailForm->setInputFilter($this->authTable->getEmailFormFilter());
            $emailForm->setData($formData);

            if($emailForm->isValid()) {
                try {
                    $data = $emailForm->getData();
                    $this->authTable->updateEmail($data['new_email'], (int) $this->authPlugin()->getAuthId());

                    $this->flashMessenger()->addSuccessMessage('Email address successfully updated');
                    return $this->redirect()->toRoute('settings', ['action' => 'email']);
                } catch(RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $emailForm]);
    }

    public function indexAction()
    {
        $auth = new AuthenticationService();
        if(!$auth->hasIdentity()) {
            return $this->redirect()->toRoute(
                'login',
                [
                    'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
                ]
            );
        }

        return new ViewModel();
    }

    public function passwordAction()
    {
        $auth = new AuthenticationService();
        if(!$auth->hasIdentity()) {
            return $this->redirect()->toRoute(
                'login',
                [
                    'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
                ]
            );
        }

        $passwordForm = new PasswordForm();
        $request = $this->getRequest();

        if($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $passwordForm->setInputFilter($this->authTable->getPasswordFormFilter());
            $passwordForm->setData($formData);

            if($passwordForm->isValid()) {
                $data = $passwordForm->getData();
                $hash = new BcryptSha();

                if ($hash->verify($data['current_password'], $this->authPlugin()->getPassword())) {
                    $this->authTable->updatePassword($data['new_password'], (int) $this->authPlugin()->getAuthId());
                    $this->flashMessenger()->addSuccessMessage('Password successfully updated');

                    return $this->redirect()->toRoute('settings', ['action' => 'password']);
                } else {
                    $this->flashMessenger()->addErrorMessage('Incorrect current password');
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $passwordForm]);
    }

    public function uploadAction()
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

        $uploadForm = new UploadForm();
        $request = $this->getRequest();

        if($request->isPost()) {
            $formData = $request->getFiles()->toArray();
            $uploadForm->setInputFilter($this->authTable->getUploadFormFilter());
            $uploadForm->setData($formData);

            if ($uploadForm->isValid()) {

                $data = $uploadForm->getData();

                if (file_exists($data['picture']['tmp_name'])) {

                    $name = basename($data['picture']['tmp_name']);
                    $name = strtolower($name);

                    $gd = new GD($data['picture']['tmp_name']);
                    $gd->adaptiveResize(200, 200)
                        ->save(BASE_PATH . DS . 'images' . DS . 'avatars' . DS .
                               $name);

                    # we want to delete the users current picture as long as it is not the default avatar.jpg
                    if ($this->authPlugin()->getPicture !== 'avatar.jpg') {
                        unlink(BASE_PATH . DS . 'images' . DS . 'avatars' .
                               DS . $this->authPlugin()->getPicture());
                    }

                    unlink($data['picture']['tmp_name']);

                    $this->authTable->updatePicture(
                        $name,
                        (int) $this->authPlugin()->getAuthId()
                    );

                    $this->flashMessenger()->addSuccessMessage('Picture successfully uploaded');
                    return $this->redirect()->toRoute('home');
                } else {
                    $this->flashMessenger()->addErrorMessage('File upload failed!');
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $uploadForm]);
    }

    public function usernameAction()
    {
        $auth = new AuthenticationService();
        if(!$auth->hasIdentity()) {
            return $this->redirect()->toRoute(
                'login',
                [
                    'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
                ]
            );
        }

        $usernameForm = new UsernameForm();
        $request = $this->getRequest();

        if($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $usernameForm->setInputFilter($this->authTable->getUsernameFormFilter());
            $usernameForm->setData($formData);

            if ($usernameForm->isValid()) {
                try {
                    $data = $usernameForm->getData();
                    $this->authTable->updateUsername($data['new_username'], (int) $this->authPlugin()->getAuthId());

                    $this->flashMessenger()->addSuccessMessage('Username successfully updated');
                    return $this->redirect()->toRoute('settings', ['action' => 'username']);
                } catch (RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $usernameForm]);
    }
}
