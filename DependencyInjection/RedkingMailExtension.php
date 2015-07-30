<?php

namespace Redking\Bundle\MailBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RedkingMailExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('services_admin.xml');
        
        if ($config['rest']['search_activities'] == true) {
            $loader->load('services_search_activities_rest.xml');
        }

        

        $container->setParameter('mailgun.key', $config['key']);
        $container->setParameter('mailgun.domain', $config['domain']);
        $container->setParameter('redking_mail.default_from', $config['default_from']);
        $container->setParameter('redking_mail.auto_record_emails', $config['auto_record_emails']);

        $definitionDecorator = new DefinitionDecorator('swiftmailer.transport.eventdispatcher.abstract');
        $container->setDefinition('mailgun.swift_transport.eventdispatcher', $definitionDecorator);

        $container->getDefinition('mailgun.swift_transport.transport')
            ->replaceArgument(0, new Reference('mailgun.swift_transport.eventdispatcher'));

        //set some alias
        $container->setAlias('mailgun', 'mailgun.swift_transport.transport');
        $container->setAlias('swiftmailer.mailer.transport.mailgun', 'mailgun.swift_transport.transport');
        $container->setAlias('swiftmailer.mailer.default.transport', 'swiftmailer.mailer.transport.mailgun');

        $delivery_enabled = true;
        try {
            $delivery_enabled = $container->getParameter('swiftmailer.mailer.default.delivery.enabled');
        } catch (InvalidArgumentException $e) {}
        
        $container->setParameter('redking_mail.delivery_enabled', $delivery_enabled);
    }
}
