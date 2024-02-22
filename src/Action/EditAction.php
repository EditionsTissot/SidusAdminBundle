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

use Sidus\AdminBundle\Admin\Action;
use Sidus\AdminBundle\Doctrine\DoctrineHelper;
use Sidus\AdminBundle\Form\FormHelper;
use Sidus\AdminBundle\Request\ValueResolver\AdminEntityValueResolver;
use Sidus\AdminBundle\Routing\RoutingHelper;
use Sidus\AdminBundle\Templating\TemplatingHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('edit', subject: 'data')]
class EditAction implements RedirectableInterface
{
    protected FormHelper $formHelper;

    protected DoctrineHelper $doctrineHelper;

    protected RoutingHelper $routingHelper;

    protected TemplatingHelper $templatingHelper;

    /** @var Action */
    protected $action;

    /** @var Action */
    protected $redirectAction;

    public function __construct(
        FormHelper $formHelper,
        DoctrineHelper $doctrineHelper,
        RoutingHelper $routingHelper,
        TemplatingHelper $templatingHelper
    ) {
        $this->formHelper = $formHelper;
        $this->doctrineHelper = $doctrineHelper;
        $this->routingHelper = $routingHelper;
        $this->templatingHelper = $templatingHelper;
    }

    public function __invoke(
        Request $request,
        #[ValueResolver(AdminEntityValueResolver::class)] $data
    ): Response
    {
        $form = $this->formHelper->getForm($this->action, $request, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrineHelper->saveEntity($this->action, $data, $request->getSession());

            return $this->routingHelper->redirectToEntity($this->redirectAction, $data, $request->query->all());
        }

        return $this->templatingHelper->renderFormAction($this->action, $form, $data);
    }

    public function setRedirectAction(Action $action): void
    {
        $this->redirectAction = $action;
    }

    public function setAction(Action $action): void
    {
        $this->action = $action;
        $this->redirectAction = $action;
    }
}
