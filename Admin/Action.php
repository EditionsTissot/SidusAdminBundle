<?php

namespace Sidus\AdminBundle\Admin;

use Symfony\Component\Routing\Route;

class Action
{
    /** @var string */
    protected $code;

    /** @var Route */
    protected $route;

    /** @var Admin */
    protected $admin;

    /** @var mixed */
    protected $formType;

    /** @var string */
    protected $template;

    /**
     * @param string $code
     * @param Admin $admin
     * @param array $c
     */
    public function __construct($code, Admin $admin, array $c)
    {
        $this->code = $code;
        $this->admin = $admin;
        $this->formType = $c['form_type'];
        $this->template = $c['template'];

        $defaults = array_merge([
            '_controller' => $admin->getController() . ':' . $code,
            '_admin' => $admin->getCode(),
        ], $c['defaults']);
        $this->route = new Route(
            $this->getAdmin()->getPrefix() . $c['path'],
            $defaults,
            $c['requirements'],
            $c['options'],
            $c['host'],
            $c['schemes'],
            $c['methods'],
            $c['condition']
        );
    }

    public function getRouteName()
    {
        return "sidus_admin.{$this->getAdmin()->getCode()}.{$this->getCode()}";
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return Admin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    public function getFormType()
    {
        if (null === $this->formType) {
            return $this->admin->getDefaultFormType();
        }
        return $this->formType;
    }
}
