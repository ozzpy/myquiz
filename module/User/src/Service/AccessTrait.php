<?php

declare(strict_types=1);

namespace User\Service;

use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\MvcEvent;
use User\Model\Table\PrivilegesTable;
use User\Model\Table\RolesTable;

trait AccessTrait
{
    public function getAccessPrivileges(MvcEvent $mvcEvent)
    {
        $services = $mvcEvent->getApplication()->getServiceManager();
        $viewAcl = new AclService($services->get(PrivilegesTable::class));
        $viewAcl->grantAccess();

        $rolesTable = $services->get(RolesTable::class);
        $guest = $rolesTable->fetchRoleByName('guest');

        $auth = new AuthenticationService();
        $roleId = !$auth->hasIdentity() ? (int)$guest->getRoleId()
            : (int)$auth->getIdentity()->role_id;
        $role = $rolesTable->fetchRoleById($roleId);

        $routeMatch = $mvcEvent->getRouteMatch();
        $resource = $routeMatch->getParam('controller') . DS . $routeMatch->getParam('action');

        //print_r($viewAcl->isAuthorized($role->getRole(), $resource)); exit(1);

        $response = $mvcEvent->getResponse();
        if ($viewAcl->isAuthorized($role->getRole(), $resource)) {
            if ($response instanceof Response) {
                if ($response->getStatusCode() != 200) {
                    $response->setStatusCode(200);
                }
            }

            return;
        }

        if (!$response instanceof Response) {
            return $response;
        }

        $response->setStatusCode(403);
        $response->setReasonPhrase('Forbidden');

        # custom handle the 403 error
        return $mvcEvent->getViewModel()->setTemplate('error/403');
    }
}
