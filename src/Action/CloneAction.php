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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sidus\AdminBundle\Admin\Action;
use Sidus\AdminBundle\Request\ValueResolver\AdminEntityValueResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Security("(is_granted('read', data) and is_granted('create', _admin.getEntity()))")
 */
class CloneAction implements ActionInjectableInterface
{
    use UpdateSubActionRedirectionTrait;

    protected EditAction $editAction;

    /** @var Action */
    protected $action;

    public function __construct(
        EditAction $editAction,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->editAction = $editAction;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function __invoke(Request $request,
                             #[ValueResolver(AdminEntityValueResolver::class)] $data): Response
    {
        $this->updateRedirectAction($this->editAction, $this->action);

        return ($this->editAction)($request, clone $data);
    }

    public function setAction(Action $action): void
    {
        $this->action = $action;
    }
}
