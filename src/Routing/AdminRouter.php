<?php declare(strict_types=1);
/*
 * This file is part of the Sidus/AdminBundle package.
 *
 * Copyright (c) 2015-2019 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\AdminBundle\Routing;

use Exception;
use LogicException;
use Sidus\AdminBundle\Admin\Admin;
use Sidus\AdminBundle\Configuration\AdminRegistry;
use Sidus\AdminBundle\Entity\AdminEntityMatcher;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use UnexpectedValueException;

/**
 * Generated path for admins and actions
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class AdminRouter
{
    /** @var AdminRegistry */
    protected AdminRegistry $adminRegistry;

    /** @var AdminEntityMatcher */
    protected AdminEntityMatcher $adminEntityMatcher;

    /** @var RouterInterface */
    protected RouterInterface $router;

    /** @var PropertyAccessorInterface */
    protected PropertyAccessorInterface $accessor;

    /**
     * @param AdminRegistry             $adminRegistry
     * @param AdminEntityMatcher        $adminEntityMatcher
     * @param RouterInterface           $router
     * @param PropertyAccessorInterface $accessor
     */
    public function __construct(
        AdminRegistry $adminRegistry,
        AdminEntityMatcher $adminEntityMatcher,
        RouterInterface $router,
        PropertyAccessorInterface $accessor
    ) {
        $this->adminRegistry = $adminRegistry;
        $this->adminEntityMatcher = $adminEntityMatcher;
        $this->router = $router;
        $this->accessor = $accessor;
    }

    /**
     * @param string|Admin $admin
     * @param string       $actionCode
     * @param array        $parameters
     * @param int          $referenceType
     *
     * @return string
     */
    public function generateAdminPath(
        $admin,
        $actionCode,
        array $parameters = [],
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $admin = $this->getAdmin($admin);
        $action = $admin->getAction($actionCode);

        $missingParams = $this->computeMissingRouteParameters($action->getRoute(), $parameters);
        foreach ($missingParams as $missingParam) {
            if ($this->router->getContext()->hasParameter($missingParam)) {
                $parameters[$missingParam] = $this->router->getContext()->getParameter($missingParam);
            }
        }

        return $this->router->generate($action->getRouteName(), $parameters, $referenceType);
    }

    /**
     * @param mixed  $entity
     * @param string $actionCode
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @return string
     */
    public function generateEntityPath(
        $entity,
        $actionCode,
        array $parameters = [],
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $admin = $this->adminEntityMatcher->getAdminForEntity($entity);

        return $this->generateAdminEntityPath($admin, $entity, $actionCode, $parameters, $referenceType);
    }

    /**
     * @param string|Admin $admin
     * @param mixed        $entity
     * @param string       $actionCode
     * @param array        $parameters
     * @param int          $referenceType
     *
     * @return string
     */
    public function generateAdminEntityPath(
        $admin,
        $entity,
        $actionCode,
        array $parameters = [],
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $admin = $this->getAdmin($admin);
        $action = $admin->getAction($actionCode);

        $missingParams = $this->computeMissingRouteParameters($action->getRoute(), $parameters);
        foreach ($missingParams as $missingParam) {
            try {
                $parameters[$missingParam] = $this->accessor->getValue($entity, $missingParam);
            } catch (Exception $e) {
                try {
                    // Fallback to array syntax
                    $parameters[$missingParam] = $this->accessor->getValue($entity, "[{$missingParam}]");
                } catch (Exception $e) {
                    $contextParam = $this->router->getContext()->getParameter($missingParam);
                    if (null !== $contextParam) {
                        $parameters[$missingParam] = $contextParam;
                    }
                }
            }
        }

        return $this->router->generate($action->getRouteName(), $parameters, $referenceType);
    }

    /**
     * @param string|Admin $admin
     *
     * @throws UnexpectedValueException
     *
     * @return Admin
     */
    protected function getAdmin($admin): Admin
    {
        if (null === $admin) {
            return $this->adminRegistry->getCurrentAdmin();
        }
        if ($admin instanceof Admin) {
            return $admin;
        }

        return $this->adminRegistry->getAdmin($admin);
    }

    /**
     * @param Route $route
     * @param array $parameters
     *
     * @throws LogicException
     *
     * @return array
     */
    protected function computeMissingRouteParameters(Route $route, array $parameters): array
    {
        $compiledRoute = $route->compile();
        $variables = array_flip($compiledRoute->getVariables());
        $mergedParams = array_replace($route->getDefaults(), $this->router->getContext()->getParameters(), $parameters);

        return array_flip(array_diff_key($variables, $mergedParams));
    }
}
