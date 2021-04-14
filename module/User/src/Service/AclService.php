<?php

declare(strict_types=1);

namespace User\Service;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Role\GenericRole;
use User\Model\Table\PrivilegesTable;

class AclService extends Acl
{
    protected $privilegesTable;

    public function __construct(PrivilegesTable $privilegesTable)
    {
        $this->privilegesTable = $privilegesTable;
    }

    public function grantAccess()
    {
        foreach ($this->privilegesTable->fetchAllResources() as $tableData) {
            if (!$this->hasRole($tableData->getRole())) {
                $role = new GenericRole($tableData->getRole());
                $this->addRole($role);
            } else {
                $role = $this->getRole($tableData->getRole());
            }

            if (!$this->hasResource($tableData->getResource())) {
                $resource = new GenericResource($tableData->getResource());
                $this->addResource($resource);
            } else {
                $resource = $this->getResource($tableData->getResource());
            }

            $this->allow($role, $resource);
        }
    }

    public function isAuthorized($role = null, $resource = null)
    {
        if (null === $role || (!$this->hasRole($role))) {
            return false;
        }

        if (null === $resource || (!$this->hasResource($resource))) {
            return false;
        }

        return $this->isAllowed($role, $resource);
    }
}
