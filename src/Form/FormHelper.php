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

namespace Sidus\AdminBundle\Form;

use Sidus\AdminBundle\Admin\Action;
use Sidus\AdminBundle\Routing\RoutingHelper;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

/**
 * Provides a simple way to access form utilities from a controller or an action
 */
class FormHelper
{
    protected RoutingHelper $routingHelper;

    protected FormFactoryInterface $formFactory;

    public function __construct(RoutingHelper $routingHelper, FormFactoryInterface $formFactory)
    {
        $this->routingHelper = $routingHelper;
        $this->formFactory = $formFactory;
    }

    public function getForm(Action $action, Request $request, $data, array $options = []): FormInterface
    {
        $dataId = $data && method_exists($data, 'getId') ? $data->getId() : null;
        $defaultOptions = $this->getDefaultFormOptions($action, $request, $dataId);

        return $this->getFormBuilder($action, $data, array_merge($defaultOptions, $options))->getForm();
    }

    public function getEmptyForm(
        Action $action,
        Request $request,
        $data
    ): FormInterface {
        $dataId = $data && method_exists($data, 'getId') ? $data->getId() : null;
        $formOptions = $this->getDefaultFormOptions($action, $request, $dataId);

        return $this->formFactory->createNamedBuilder(
            "form_{$action->getAdmin()->getCode()}_{$action->getCode()}",
            FormType::class,
            null,
            $formOptions
        )->getForm();
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getFormBuilder(Action $action, $data, array $options = []): FormBuilderInterface
    {
        if (!$action->getFormType()) {
            throw new UnexpectedValueException("Missing parameter 'form_type' for action '{$action->getCode()}'");
        }

        return $this->formFactory->createNamedBuilder(
            "form_{$action->getAdmin()->getCode()}_{$action->getCode()}",
            $action->getFormType(),
            $data,
            $options
        );
    }

    /**
     * @param null $dataId
     */
    public function getDefaultFormOptions(Action $action, Request $request, $dataId = null): array
    {
        $dataId = $dataId ?: 'new';

        return array_merge(
            $action->getFormOptions(),
            [
                'action' => $this->routingHelper->getCurrentUri($action, $request),
                'attr' => [
                    'novalidate' => 'novalidate',
                    'id' => "form_{$action->getAdmin()->getCode()}_{$action->getCode()}_{$dataId}",
                ],
                'method' => 'post',
            ]
        );
    }
}
