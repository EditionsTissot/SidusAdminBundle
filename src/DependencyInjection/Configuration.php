<?php

declare(strict_types=1); /** @noinspection NullPointerExceptionInspection */
/*
 * This file is part of the Sidus/AdminBundle package.
 *
 * Copyright (c) 2015-2019 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\AdminBundle\DependencyInjection;

use RuntimeException;
use Sidus\AdminBundle\Admin\Action;
use Sidus\AdminBundle\Admin\Admin;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    protected string $root;

    /**
     * @param string $root
     */
    public function __construct($root = 'sidus_admin')
    {
        $this->root = $root;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->root);
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('base_template')->defaultValue('@SidusAdmin/base.html.twig')->end()
            ->scalarNode('admin_class')->defaultValue(Admin::class)->end()
            ->scalarNode('action_class')->defaultValue(Action::class)->end()
            ->append($this->getAdminConfigTreeBuilder())
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @throws RuntimeException
     */
    protected function getAdminConfigTreeBuilder(): NodeDefinition
    {
        $builder = new TreeBuilder('configurations');
        $node = $builder->getRootNode();
        $adminDefinition = $node
            ->useAttributeAsKey('code')
            ->prototype('array')
            ->performNoDeepMerging()
            ->children()
        ;

        $this->appendAdminDefinition($adminDefinition);

        $adminDefinition->end()
            ->end()
            ->end()
        ;

        return $node;
    }

    protected function appendAdminDefinition(NodeBuilder $adminDefinition): void
    {
        $actionDefinition = $adminDefinition
            ->scalarNode('controller')->defaultNull()->end()
            ->arrayNode('controller_pattern')->defaultValue([])->scalarPrototype()->end()->end()
            ->scalarNode('base_template')->defaultNull()->end()
            ->arrayNode('template_pattern')->defaultValue([])->scalarPrototype()->end()->end()
            ->scalarNode('prefix')->isRequired()->end()
            ->scalarNode('entity')->isRequired()->end()
            ->scalarNode('action_class')->end()
            ->scalarNode('form_type')->defaultNull()->end()
            ->variableNode('options')->defaultValue([])->end()
            ->arrayNode('actions')
            ->useAttributeAsKey('code')
            ->prototype('array')
            ->performNoDeepMerging()
            ->children()
        ;

        $this->appendActionDefinition($actionDefinition);

        $actionDefinition->end()
            ->end()
            ->end()
        ;
    }

    protected function appendActionDefinition(NodeBuilder $actionDefinition): void
    {
        $actionDefinition
            // Custom parameters
            ->scalarNode('form_type')->defaultNull()->end()
            ->variableNode('form_options')->defaultValue([])->end()
            ->scalarNode('base_template')->defaultNull()->end()
            ->scalarNode('template')->defaultNull()->end()
            // Default route parameters
            ->scalarNode('path')->isRequired()->end()
            ->variableNode('defaults')->defaultValue([])->end()
            ->variableNode('requirements')->defaultValue([])->end()
            ->variableNode('options')->defaultValue([])->end()
            ->scalarNode('host')->defaultValue('')->end()
            ->variableNode('schemes')->defaultValue([])->end()
            ->variableNode('methods')->defaultValue([])->end()
            ->scalarNode('condition')->defaultNull()->end();
    }
}
