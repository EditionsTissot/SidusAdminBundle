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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sidus\AdminBundle\Admin\Action;
use Sidus\AdminBundle\DataGrid\DataGridHelper;
use Sidus\AdminBundle\Templating\TemplatingHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Security("is_granted('list', _admin.getEntity())")
 */
class ListAction implements ActionInjectableInterface
{
    protected DataGridHelper $dataGridHelper;

    protected TemplatingHelper $templatingHelper;

    protected RouterInterface $router;

    /** @var Action */
    protected $action;

    public function __construct(
        DataGridHelper $dataGridHelper,
        TemplatingHelper $templatingHelper,
        RouterInterface $router
    ) {
        $this->dataGridHelper = $dataGridHelper;
        $this->templatingHelper = $templatingHelper;
        $this->router = $router;
    }

    public function __invoke(Request $request): Response
    {
        $dataGrid = $this->dataGridHelper->bindDataGridRequest($this->action, $request);

        return $this->templatingHelper->renderListAction($this->action, $dataGrid);
    }

    public function setAction(Action $action): void
    {
        $this->action = $action;
    }
}
