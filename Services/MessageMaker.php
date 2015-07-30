<?php

namespace Redking\Bundle\MailBundle\Services;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class MessageMaker
{
    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    protected $dm;

    /**
     * @var TwigEngine
     */
    protected $twig;

    /**
     * @var string
     */
    protected $template_class;

    public function __construct(ManagerRegistry $doctrine, TwigEngine $twig, $template_class)
    {
        $this->dm             = $doctrine->getManager();
        $this->twig           = $twig;
        $this->template_class = $template_class;
    }

    /**
     * Retourne un message pour un template en base
     * @param  string $template_name nom du template
     * @param  string $locale        langue
     * @param  array  $template_vars tableau de variable
     * @return SwiftMessage
     */
    public function getMessageForTemplate($template_name, $locale = 'en', $template_vars = array())
    {
        $template = $this->dm->getRepository($this->template_class)->find($template_name);

        if (is_null($template)) {
            throw new \Exception(sprintf('Template "%s" not found', $template_name));
        }

        // Refresh template based on locale
        $template->setTranslatableLocale($locale);
        $this->dm->refresh($template);

        // Assign template engine
        $template->setTwigEngine($this->twig);

        // Construct and return message
        return \Swift_Message::newInstance()
            ->setSubject($template->getFinalSubject($template_vars))
            ->setBody($template->getFinalBody($template_vars), 'text/html')
        ;
    }

    /**
     * Retourne un message construit à partir des body et subject
     * @param  string $body          
     * @param  string $subject       
     * @param  array  $template_vars [description]
     * @return SwiftMessage                
     */
    public function getMessageFromData($body, $subject, $template_vars = array())
    {
        $instantiator = new \Doctrine\Instantiator\Instantiator();

        $template = $instantiator->instantiate($this->template_class);

        $template->setTwigEngine($this->twig);
        $template->setBody($body);
        $template->setSubject($subject);

        // Construct and return message
        return \Swift_Message::newInstance()
            ->setSubject($template->getFinalSubject($template_vars))
            ->setBody($template->getFinalBody($template_vars), 'text/html')
        ;
    }
}
