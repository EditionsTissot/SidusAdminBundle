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

namespace Sidus\AdminBundle\Action;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sidus\AdminBundle\Admin\Action;
use Sidus\AdminBundle\Form\FormHelper;
use Sidus\AdminBundle\Templating\TemplatingHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class to implement empty form actions
 */
abstract class AbstractEmptyFormAction implements ActionInjectableInterface
{
    protected FormHelper $formHelper;

    protected TemplatingHelper $templatingHelper;

    /** @var Action */
    protected $action;

    public function __construct(
        FormHelper $formHelper,
        TemplatingHelper $templatingHelper
    ) {
        $this->formHelper = $formHelper;
        $this->templatingHelper = $templatingHelper;
    }

    /**
     * @ParamConverter(name="data", converter="sidus_admin.entity")
     */
    public function __invoke(Request $request, $data): Response
    {
        $dataId = $data->getId();
        $form = $this->formHelper->getEmptyForm($this->action, $request, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->applyAction($request, $form, $data);
        }

        return $this->templatingHelper->renderFormAction(
            $this->action,
            $form,
            $data,
            [
                'dataId' => $dataId,
            ]
        );
    }

    public function setAction(Action $action): void
    {
        $this->action = $action;
    }

    abstract protected function applyAction(Request $request, FormInterface $form, $data): Response;
}
