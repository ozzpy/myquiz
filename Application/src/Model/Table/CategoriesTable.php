<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\AbstractTableGateway;

class CategoriesTable extends AbstractTableGateway
{
    protected $table = 'app_categories';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAllCategories()
    { 
        $sqlQuery = $this->sql->select()->order('category ASC');
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        $row = [];
        
        foreach ($sqlStmt->execute() as $column) {
            $row[$column['category_id']] = $column['category'];
        }

        return $row;
    }
}
