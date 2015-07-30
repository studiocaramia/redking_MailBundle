<?php

namespace Redking\Bundle\MailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        $rootNode = $treeBuilder->root('redking_mail');

        $this->addAPIConfigSection($rootNode);

        $rootNode
            ->children()
                ->arrayNode('rest')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('search_activities')->defaultTrue()->end()
                        ->scalarNode('messaging')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()

            ->children()
                ->scalarNode('auto_record_emails')
                ->defaultValue(true)
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * Add Mailgun credentials
     * @param ArrayNodeDefinition $rootNode [description]
     */
    private function addAPIConfigSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->scalarNode('key')
                ->isRequired()
            ->end()
            ->scalarNode('domain')
                ->isRequired()
            ->end()
            ->scalarNode('default_from')->defaultNull()->end()
        ;
    }
}
