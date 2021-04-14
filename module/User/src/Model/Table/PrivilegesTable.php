<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Hydrator\ClassMethodsHydrator;
use User\Model\Entity\PrivilegeEntity;

class PrivilegesTable extends AbstractTableGateway
{
    protected $table = 'user_privileges';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAllResources()
    {
        $sqlQuery = $this->sql->select()
            ->join(
                'user_resources',
                'user_resources.resource_id='
                . $this->table . '.resource_id',
                ['module', 'controller', 'action']
            )
            ->join(
                'user_roles',
                'user_roles.role_id=' . $this->table . '.role_id',
                'role'
            );

        $sqlQuery = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler  = $sqlQuery->execute();

        $entity = new PrivilegeEntity();
        $method = new ClassMethodsHydrator();

        $resultSet = new HydratingResultSet($method, $entity);
        $resultSet->initialize($handler);

        return $resultSet;
    }
}
