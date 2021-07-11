<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\BcryptSha;
use Laminas\Db\Adapter\Adapter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\SessionManager;
use Laminas\View\Model\ViewModel;
use User\Form\Auth\LoginForm;
use User\Model\Table\AuthTable;
use User\Model\UrlModel;

class LoginController extends AbstractActionController
{
    private $adapter;
    private $authTable;

    public function __construct(
        Adapter $adapter,
        AuthTable $authTable
    ) {
        $this->adapter = $adapter;
        $this->authTable = $authTable;
    }

    public function indexAction()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $loginForm = new LoginForm();
        $loginForm->get('returnUrl')->setValue(
            $this->getEvent()->getRouteMatch()->getParam('returnUrl')
        );
        
        $request = $this->getRequest();

        if ($request->isPost()) {

            $formData = $request->getPost()->toArray();
            $loginForm->setInputFilter($this->authTable->getLoginFormFilter());
            $loginForm->setData($formData);

            if ($loginForm->isValid()) {

                $authAdapter = new CredentialTreatmentAdapter($this->adapter);
                $authAdapter->setTableName($this->authTable->getTable())
                    ->setIdentityColumn('email')
                    ->setCredentialColumn('password');

                $data = $loginForm->getData();
                $returnUrl = $this->params()->fromPost('returnUrl');
                //$returnUrl = $data['returnUrl'];

                $authAdapter->setIdentity($data['email']);

                $hash = new BcryptSha();
                $info = $this->authTable->fetchAccountByEmail($data['email']);

                if ($hash->verify($data['password'], $info->getPassword())) {
                    $authAdapter->setCredential($info->getPassword());
                } else {
                    $authAdapter->setCredential('');
                }

                $authResult = $auth->authenticate($authAdapter);

                switch ($authResult->getCode()) {

                    case Result::FAILURE_CREDENTIAL_INVALID:
                        $this->flashMessenger()->addErrorMessage('Incorrect Password');
                        return $this->preserveUrl($returnUrl);
                        break;

                    case Result::SUCCESS:

                        if ($data['recall'] === 1) {
                            $ssm = new SessionManager();
                            $ttl = 1814400; # 21 days time to live
                            $ssm->rememberMe($ttl);
                        }

                        $storage = $auth->getStorage();
                        $returnColumns = ['auth_id', 'role_id'];
                        $storage->write(
                            $authAdapter->getResultRowObject($returnColumns)
                        );

                        if (!empty($returnUrl)) {
                            return $this->redirect()->toUrl(
                                UrlModel::decode($returnUrl)
                            );
                        }

                        return $this->redirect()->toRoute(
                            'profile',
                            [
                                'id' => $info->getAuthId(),
                                'username' => mb_strtolower($info->getUsername())
                            ]
                        );

                        break;

                    default:
                        $this->flashMessenger()->addErrorMessage('Authentication failed');
                        return $this->preserveUrl($returnUrl);
                        break;
                }
            }
        }

        return (new ViewModel(['form' => $loginForm]))->setTemplate('user/auth/login');
    }

    private function preserveUrl(string $returnUrl = null)
    {
        if (empty($returnUrl)) {
            return $this->redirect()->refresh();
        }

        return $this->redirect()->toUrl(UrlModel::decode($returnUrl));
    }
}
