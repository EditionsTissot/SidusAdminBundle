<?php

declare(strict_types=1);
/*
 * This file is part of the Sidus/AdminBundle package.
 *
 * Copyright (c) 2015-2019 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\AdminBundle\Routing;

use RuntimeException;
use Sidus\AdminBundle\Configuration\AdminRegistry;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads all routes contained in actions
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class AdminRouteLoader extends Loader
{
    protected AdminRegistry $adminRegistry;

    public function __construct(AdminRegistry $adminRegistry)
    {
        parent::__construct();
        $this->adminRegistry = $adminRegistry;
    }

    /**
     * @param null $type
     *
     * @throws RuntimeException
     */
    public function load($resource, $type = null): RouteCollection
    {
        $routes = new RouteCollection();

        foreach ($this->adminRegistry->getAdmins() as $admin) {
            foreach ($admin->getActions() as $action) {
                $routes->add($action->getRouteName(), $action->getRoute());
            }
        }

        return $routes;
    }

    /**
     * @param string $type
     */
    public function supports($resource, string $type = null): bool
    {
        return 'sidus_admin' === $type;
    }
}
