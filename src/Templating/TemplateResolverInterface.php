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
use Twig\Template;

/**
 * Services implementing this interface must be able to resolve a template based on an action configuration
 */
interface TemplateResolverInterface
{
    /**
     * @param string $templateType
     */
    public function getTemplate(Action $action, $templateType = 'html'): Template;
}
