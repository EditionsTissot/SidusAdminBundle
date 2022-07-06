<?php declare(strict_types=1);
/*
 * This file is part of the Sidus/AdminBundle package.
 *
 * Copyright (c) 2015-2019 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\AdminBundle\Action;

use Sidus\AdminBundle\Templating\TemplatingHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sidus\AdminBundle\Admin\Action;
use Sidus\AdminBundle\Doctrine\DoctrineHelper;
use Sidus\AdminBundle\Form\FormHelper;
use Sidus\AdminBundle\Routing\RoutingHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("is_granted('edit', data)")
 */
class EditAction implements RedirectableInterface
{
    /** @var FormHelper */
    protected FormHelper $formHelper;

    /** @var DoctrineHelper */
    protected DoctrineHelper $doctrineHelper;

    /** @var RoutingHelper */
    protected RoutingHelper $routingHelper;

    /** @var TemplatingHelper */
    protected TemplatingHelper $templatingHelper;

    /** @var Action */
    protected $action;

    /** @var Action */
    protected $redirectAction;

    /**
     * @param FormHelper       $formHelper
     * @param DoctrineHelper   $doctrineHelper
     * @param RoutingHelper    $routingHelper
     * @param TemplatingHelper $templatingHelper
     */
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

    /**
     * @ParamConverter(name="data", converter="sidus_admin.entity")
     *
     * @param Request $request
     * @param mixed   $data
     *
     * @return Response
     */
    public function __invoke(Request $request, $data): Response
    {
        $form = $this->formHelper->getForm($this->action, $request, $data);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrineHelper->saveEntity($this->action, $data, $request->getSession());

            return $this->routingHelper->redirectToEntity($this->redirectAction, $data, $request->query->all());
        }

        return $this->templatingHelper->renderFormAction($this->action, $form, $data);
    }

    /**
     * @param Action $action
     */
    public function setRedirectAction(Action $action): void
    {
        $this->redirectAction = $action;
    }

    /**
     * @param Action $action
     */
    public function setAction(Action $action): void
    {
        $this->action = $action;
        $this->redirectAction = $action;
    }
}
