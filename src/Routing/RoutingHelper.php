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

use Sidus\AdminBundle\Admin\Action;
use Sidus\AdminBundle\Admin\Admin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Provides a simple way to access routing utilities from a controller or an action
 */
class RoutingHelper
{
    protected AdminRouter $adminRouter;

    public function __construct(AdminRouter $adminRouter)
    {
        $this->adminRouter = $adminRouter;
    }

    /**
     * @param int $referenceType
     * @param int $status
     */
    public function redirectToEntity(
        Action $action,
        $entity,
        array $parameters = [],
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
        $status = 302
    ): RedirectResponse {
        $url = $this->adminRouter->generateAdminEntityPath(
            $action->getAdmin(),
            $entity,
            $action->getCode(),
            $parameters,
            $referenceType
        );

        return new RedirectResponse($url, $status);
    }

    /**
     * @param int $referenceType
     * @param int $status
     */
    public function redirectToAction(
        Action $action,
        array $parameters = [],
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
        $status = 302
    ): RedirectResponse {
        $url = $this->adminRouter->generateAdminPath(
            $action->getAdmin(),
            $action->getCode(),
            $parameters,
            $referenceType
        );

        return new RedirectResponse($url, $status);
    }

    public function getAdminListPath(Admin $admin, array $parameters = []): ?string
    {
        if (!$admin->hasAction('list')) {
            return null;
        }

        return $this->adminRouter->generateAdminPath($admin, 'list', $parameters);
    }

    public function getCurrentUri(Action $action, Request $request, array $parameters = []): string
    {
        if ($request->attributes->get('_route') === $action->getRouteName()) {
            $parameters = array_merge(
                $request->attributes->get('_route_params'),
                $parameters
            );
        }

        return $this->adminRouter->generateAdminPath($action->getAdmin(), $action->getCode(), $parameters);
    }
}
