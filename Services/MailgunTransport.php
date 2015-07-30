<?php

namespace Redking\Bundle\MailBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mailgun\Mailgun;

use \Swift_Events_EventListener;
use \Swift_Events_SendEvent;
use \Swift_Mime_HeaderSet;
use \Swift_Mime_Message;
use \Swift_Transport;


class MailgunTransport implements Swift_Transport
{
    /**
     * @var \Mailgun\Mailgun mailgun
     */
    private $mailgun;

    /**
     * @var string domain
     */
    private $domain;

    /**
     * The event dispatcher from the plugin API
     *
     * @var \Swift_Events_EventDispatcher eventDispatcher
     */
    private $eventDispatcher;

    /**
     * Expediteur par defaut
     * @var string
     */
    protected $default_from;

    /**
     * Active ou non l'envoi effectif
     * @var boolean
     */
    protected $delivery_enabled = true;

    /**
     * @param \Swift_Events_EventDispatcher $eventDispatcher
     * @param Mailgun $mailgun
     * @param $domain
     */
    public function __construct(\Swift_Events_EventDispatcher $eventDispatcher, Mailgun $mailgun, $domain)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->domain = $domain;
        $this->mailgun = $mailgun;
    }

    /**
     * [setDefaultFrom description]
     * @param [type] $default_from [description]
     */
    public function setDefaultFrom($default_from)
    {
        $this->default_from = $default_from;
    }

    /**
     * [getDefaultFrom description]
     * @return [type] [description]
     */
    public function getDefaultFrom()
    {
        return $this->default_from;
    }

    /**
     * [setDeliveryEnabled description]
     * @param boolean $delivery_enabled [description]
     */
    public function setDeliveryEnabled($delivery_enabled)
    {
        $this->delivery_enabled = $delivery_enabled;
    }

    /**
     * [isDeliveryEnabled description]
     * @return boolean [description]
     */
    public function isDeliveryEnabled()
    {
        return $this->delivery_enabled;
    }

    /**
     * Not used.
     */
    public function isStarted()
    {
    	return true;
    }

    /**
     * Not used.
     */
    public function start()
    {
    }

    /**
     * Not used.
     */
    public function stop()
    {
    }

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param Swift_Mime_Message $message
     * @param string[]           $failedRecipients An array of failures by-reference
     *
     * @return integer
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null, $options = array())
    {
        class_exists('Swift_Mailer'); // just to pass condition in MessageDataCollector

        $default_options = [
            'open_tracking' => true,
            'click_tracking' => false,
        ];
        $options = array_merge($default_options, $options);

        if ($evt = $this->eventDispatcher->createSendEvent($this, $message)) {
            $this->eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        // Ajout automatique de la version texte si le mail ne contient que du HTML
        if ($message->getContentType() == 'text/html') {
            $text = new \Html2Text\Html2Text($message->getBody());
            $message->addPart($text->getText(), 'text/plain');
        }

    	$fromHeader = $message->getHeaders()->get('From');
        if ($fromHeader->getFieldBody() == '' && $this->getDefaultFrom() != null) {
            $message->setFrom($this->getDefaultFrom());
        }
    	$toHeader = $message->getHeaders()->get('To');

        if (!$toHeader) {
            throw new \Swift_TransportException(
                'Cannot send message without a recipient'
            );
        }

        $from = $fromHeader->getFieldBody();
        $to = $toHeader->getFieldBody();

        $message_data = ['from' => $from, 'to' => $to];

        // Ajout d'options dans les trackings
        if ($options['open_tracking'] === true) {
            $message_data['o:tracking-opens'] = 'yes';
        }

        if ($this->isDeliveryEnabled()) {
            $result = $this->mailgun->sendMessage($this->domain, $message_data, $message->toString());
            $success = $result->http_response_code == 200;
        } else {
            $success = true;
        }

        if ($evt) {
            $evt->setResult($success ? Swift_Events_SendEvent::RESULT_SUCCESS : Swift_Events_SendEvent::RESULT_FAILED);
            $this->eventDispatcher->dispatchEvent($evt, 'sendPerformed');
        }

        return 1;
    }

    /**
     * Register a plugin in the Transport.
     *
     * @param Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->eventDispatcher->bindEventListener($plugin);
    }
}
