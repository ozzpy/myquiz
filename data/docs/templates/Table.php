<?php

declare(strict_types=1);

namespace ModuleName\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\AbstractTableGateway;

class ClassNameTable extends AbstractTableGateway
{
    protected $table = 'module_tableName';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
}
