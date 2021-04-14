<?php

declare(strict_types=1);

namespace User\Model\Entity;

class PrivilegeEntity
{
    protected $resource_id;
    protected $role_id;
    #user_resources table columns
    protected $module;
    protected $controller;
    protected $action;
    #user_roles table columns
    protected $role;

    /**
     * @return mixed
     */
    public function getResourceId()
    {
        return $this->resource_id;
    }

    /**
     * @param mixed $resource_id
     *
     * @return PrivilegeEntity
     */
    public function setResourceId($resource_id)
    {
        $this->resource_id = $resource_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * @param mixed $role_id
     *
     * @return PrivilegeEntity
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     *
     * @return PrivilegeEntity
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     *
     * @return PrivilegeEntity
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     *
     * @return PrivilegeEntity
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     *
     * @return PrivilegeEntity
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    public function getResource()
    {
        # we seek to get a string in the form similar to - User\Controller\AuthController\create
        return ucfirst($this->getModule()) . DS . 'Controller' . 
          DS . ucfirst($this->getController()) . 'Controller' . DS . strtolower($this->getAction());
    }
}
