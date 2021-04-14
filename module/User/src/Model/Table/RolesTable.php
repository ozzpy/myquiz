<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Hydrator\ClassMethodsHydrator;
use User\Model\Entity\RoleEntity;

class RolesTable extends AbstractTableGateway
{
    protected $table = 'user_roles';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAllRoles()
    {
        $sqlQuery = $this->sql->select()->order('role_id ASC');
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $results  = $sqlStmt->execute();

        $entity   = new RoleEntity();
        $method   = new ClassMethodsHydrator();

        $resultSet = new HydratingResultSet($method, $entity);
        $resultSet->initialize($results);

        return $resultSet;
    }

    public function fetchRoleById(int $roleId)
    {
        $sqlQuery = $this->sql->select()->where(['role_id' => $roleId]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $result   = $sqlStmt->execute()->current();

        if (!$result) {
            return null;
        }

        $method = new ClassMethodsHydrator();
        $entity = new RoleEntity();
        $method->hydrate($result, $entity);

        return $entity;
    }

    public function fetchRoleByName(string $role)
    {
        $sqlQuery = $this->sql->select()->where(['role' => $role]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $result   = $sqlStmt->execute()->current();

        if (!$result) {
            return null;
        }

        $method = new ClassMethodsHydrator();
        $entity = new RoleEntity();
        $method->hydrate($result, $entity);

        return $entity;
    }
}
