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

use function count;

use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Sidus\AdminBundle\Admin\Action;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Template;

/**
 * Resolve templates based on admin configuration
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class TemplateResolver implements TemplateResolverInterface
{
    protected Environment $twig;

    protected LoggerInterface $logger;

    public function __construct(
        Environment $twig,
        LoggerInterface $logger
    ) {
        $this->twig = $twig;
        $this->logger = $logger;
    }

    /**
     * @param string $format
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getTemplate(Action $action, $format = 'html'): Template
    {
        $admin = $action->getAdmin();

        if ($action->getTemplate()) {
            // If the template was specified, use this one
            return $this->twig->loadTemplate($action->getTemplate());
        }

        // Priority to new template_pattern system:
        if (0 === count($admin->getTemplatePattern())) {
            throw new LogicException("No template configured for action {$admin->getCode()}.{$action->getCode()}");
        }

        foreach ($admin->getTemplatePattern() as $templatePattern) {
            $template = strtr(
                $templatePattern,
                [
                    '{{admin}}' => lcfirst($admin->getCode()),
                    '{{Admin}}' => ucfirst($admin->getCode()),
                    '{{action}}' => lcfirst($action->getCode()),
                    '{{Action}}' => ucfirst($action->getCode()),
                    '{{format}}' => $format,
                ]
            );

            try {
                return $this->twig->loadTemplate($template);
            } catch (LoaderError $mainError) {
                $this->logger->debug("Unable to load template '{$template}': {$mainError->getMessage()}");
                continue;
            }
        }

        $flattened = implode(', ', $admin->getTemplatePattern());
        throw new RuntimeException("Unable to resolve any valid template for the template_pattern configuration: {$flattened}");
    }
}
