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

namespace Sidus\AdminBundle\Templating;

use Sidus\AdminBundle\Admin\Action;
use Sidus\AdminBundle\Routing\RoutingHelper;
use Sidus\DataGridBundle\Model\DataGrid;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides a simple way to access rendering utilities from a controller or an action
 */
class TemplatingHelper
{
    protected TemplateResolverInterface $templateResolver;

    protected RoutingHelper $routingHelper;

    public function __construct(TemplateResolverInterface $templateResolver, RoutingHelper $routingHelper)
    {
        $this->templateResolver = $templateResolver;
        $this->routingHelper = $routingHelper;
    }

    public function renderAction(Action $action, array $parameters = []): Response
    {
        $response = new Response();
        /* @noinspection PhpUnhandledExceptionInspection */
        $response->setContent($this->templateResolver->getTemplate($action)->render($parameters));

        return $response;
    }

    public function renderListAction(
        Action $action,
        DataGrid $dataGrid,
        array $viewParameters = []
    ): Response {
        $viewParameters = array_merge(
            $this->getViewParameters($action),
            ['datagrid' => $dataGrid],
            $viewParameters
        );

        return $this->renderAction($action, $viewParameters);
    }

    /**
     * @param null $data
     */
    public function renderFormAction(
        Action $action,
        FormInterface $form,
        $data = null,
        array $viewParameters = []
    ): Response {
        $viewParameters = array_merge($this->getViewParameters($action, $form, $data), $viewParameters);

        return $this->renderAction($action, $viewParameters);
    }

    /**
     * @param FormInterface $form
     */
    public function getViewParameters(
        Action $action,
        FormInterface $form = null,
        $data = null,
        array $listRouteParameters = []
    ): array {
        $parameters = [
            'listPath' => $this->routingHelper->getAdminListPath($action->getAdmin(), $listRouteParameters),
            'action' => $action,
            'admin' => $action->getAdmin(),
        ];

        if ($form) {
            $parameters['form'] = $form->createView();
        }

        if ($data) {
            $parameters['data'] = $data;
        }

        return $parameters;
    }
}
