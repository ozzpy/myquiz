<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\AbstractTableGateway;

class ForgotTable extends AbstractTableGateway
{
    protected $table = 'user_forgot';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function clearOldTokens()
    {
        $sqlQuery = $this->sql->delete()->where(
            ['created < ?' => date('Y-m-d H:i:s', time() - (3600 * 24))]
        );
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function deleteToken(int $authId)
    {
        $sqlQuery = $this->sql->delete()->where(['auth_id' => $authId]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function fetchToken(string $token, int $authId)
    {
        $sqlQuery = $this->sql->select()->where(['token' => $token])->where(
            ['auth_id' => $authId]
        );
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute()->current();
    }

    public function generateToken(int $length)
    {
        if($length < 8 || $length > 40) {
            throw new \LengthException('Token length must be in range 08-40.');
        }

        # allowed characters
        $chars = 'NaObPcQdReSfTgUhViWjXkYlZmAnBoCpDqErFsGtHuIvJwKxLyMz';
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $random = rand(0, strlen($chars) - 1);
            $token .= substr($chars, $random, 1);
        }

        return $token;
    }

    public function insertToken(string $token, int $authId)
    {
        $values = [
            'auth_id' => $authId,
            'token'   => $token,
            'created' => date('Y-m-d H:i:s')
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }
}
