<?php
/**
 * Subscriber pour les events REST
 */
namespace Redking\Bundle\MailBundle\EventListener;

class SwiftEventListener implements \Swift_Events_SendListener 
{
    /**
     * Doctrine\Common\Persistence\ObjectManager
     * @var [type]
     */
    protected $dm;

    /**
     * @var boolean
     */
    protected $enabled;

    public function __construct($doctrine, $activity_class, $enabled)
    {
        $this->dm             = $doctrine->getManager();
        $this->activity_class = $activity_class;
        $this->enabled        = $enabled;
    }

    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {

    }

    /**
     * Gestion des messages apres leur envoi
     * @param  Swift_Events_SendEvent  $event [description]
     * @return [type]        [description]
     */
    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
        if ($evt->getResult() === \Swift_Events_SendEvent::RESULT_SUCCESS && $this->enabled == true) {
            
            // Création de l'activity
            $activity = $this->createActivity($evt->getMessage());

            $this->dm->persist($activity);
            $this->dm->flush();
        }
    }


    /**
     * Create activity from Swift Message
     * @param  \Swift_Message $message 
     * @return EmailActivity
     */
    protected function createActivity(\Swift_Message $message)
    {
        // Creation du doc de trace d'activité avec les données basiques
        $from_email = $message->getHeaders()->get('From')->getFieldBody();
        $to_email   = $message->getHeaders()->get('To')->getFieldBody();
        $message_id = $message->getHeaders()->get('Message-ID')->getId();
        $subject    = $message->getSubject();

        $activity = new $this->activity_class();
        $activity->setFromEmail($from_email);
        $activity->setToEmail($to_email);
        $activity->setSubject($subject);
        $activity->setMessageId($message_id);


        // Récupération du document associé aux relations from_user et to_user
        $meta = $this->dm->getClassMetadata($this->activity_class);
        
        $class = $meta->getAssociationTargetClass('from_user');
        $repo = $this->dm->getRepository($class);
        
        $user = $repo->findOneByEmail($activity->getFromEmail());
        if (!is_null($user)) {
            $activity->setFromUser($user);
        }

        $class = $meta->getAssociationTargetClass('to_user');
        $repo = $this->dm->getRepository($class);
        
        $user = $repo->findOneByEmail($activity->getToEmail());

        if (!is_null($user)) {
            $activity->setToUser($user);
        }

        return $activity;

    }
}
