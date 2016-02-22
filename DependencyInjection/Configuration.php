<?php

namespace A5sys\PdfBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('a5sys_pdf');

        $rootNode
            ->children()
                ->scalarNode('binary')->isRequired()->end()
                ->scalarNode('temp_dir')->defaultNull()->end()
                ->scalarNode('encoding')->defaultValue('UTF-8')->end()
                ->arrayNode('command_options')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('use_exec')->defaultTrue()->end()
                        ->scalarNode('escape_args')->defaultFalse()->end()
                        ->arrayNode('proc_options')
                            ->children()
                                ->scalarNode('bypass_shell')->defaultTrue()->end()
                                ->scalarNode('suppress_errors')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
