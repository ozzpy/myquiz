<?php

declare(strict_types=1);

namespace User\Plugin;

use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Mvc\Plugin\Identity\Identity;
use User\Model\Table\AuthTable;

class AuthPlugin extends Identity
{
    protected $authTable;

    public function __invoke()
    {
        if (! $this->authTable instanceof AuthTable) {
            throw new \RuntimeException(
                'No AuthTable instance provided; cannot fetch user data'
            );
        }

        if (! $this->authenticationService instanceof
            AuthenticationServiceInterface) {
            throw new \RuntimeException(
                'No AuthenticationServiceInterface instance provided; cannot lookup identity'
            );
        }

        if (! $this->authenticationService->hasIdentity()) {
            return;
        }

        return $this->getAuthTable()->fetchAccountByAuthId(
            (int) $this->authenticationService->getIdentity()->auth_id
        );
    }

    public function getAuthTable()
    {
        return $this->authTable;
    }

    public function setAuthTable(AuthTable $authTable)
    {
        $this->authTable = $authTable;
        return $this;
    }
}
