<?php

namespace Redking\Bundle\MailBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Email logs
 *
 * @MongoDB\Document(collection="email_activity")
 */
class EmailActivity
{
    /**
     * @MongoDB\Id(strategy="INCREMENT")
     */
    protected $id;

    /**
     * @MongoDB\Date
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $date;

    /**
     * @MongoDB\String
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $from_email;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sonata\UserBundle\Model\UserInterface")
     * @Serializer\Accessor(getter="getFromUserDrawback")
     */
    protected $from_user;

    /**
     * @MongoDB\String
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $to_email;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sonata\UserBundle\Model\UserInterface")
     * @Serializer\Accessor(getter="getToUserDrawback")
     */
    protected $to_user;

    /**
     * @MongoDB\String
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $subject;

    /**
     * @MongoDB\String
     * @MongoDB\Index(unique=true, order="asc")
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $message_id;

    /**
     * @MongoDB\Boolean
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $opened = false;

    public function __construct()
    {
        $this->setDate(new \DateTime());
    }

    /**
     * Get id
     *
     * @return int_id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param date $date
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set fromEmail
     *
     * @param string $fromEmail
     * @return self
     */
    public function setFromEmail($fromEmail)
    {
        $this->from_email = $fromEmail;
        return $this;
    }

    /**
     * Get fromEmail
     *
     * @return string $fromEmail
     */
    public function getFromEmail()
    {
        return $this->from_email;
    }

    /**
     * Set fromUser
     *
     * @param Sonata\UserBundle\Model\UserInterface $fromUser
     * @return self
     */
    public function setFromUser(\Sonata\UserBundle\Model\UserInterface $fromUser)
    {
        $this->from_user = $fromUser;
        return $this;
    }

    /**
     * Get fromUser
     *
     * @return Sonata\UserBundle\Model\UserInterface $fromUser
     */
    public function getFromUser()
    {
        return $this->from_user;
    }

    /**
     * Set toEmail
     *
     * @param string $toEmail
     * @return self
     */
    public function setToEmail($toEmail)
    {
        $this->to_email = $toEmail;
        return $this;
    }

    /**
     * Get toEmail
     *
     * @return string $toEmail
     */
    public function getToEmail()
    {
        return $this->to_email;
    }

    /**
     * Set toUser
     *
     * @param Sonata\UserBundle\Model\UserInterface $toUser
     * @return self
     */
    public function setToUser(\Sonata\UserBundle\Model\UserInterface $toUser)
    {
        $this->to_user = $toUser;
        return $this;
    }

    /**
     * Get toUser
     *
     * @return Sonata\UserBundle\Model\UserInterface $toUser
     */
    public function getToUser()
    {
        return $this->to_user;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Get subject
     *
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set messageId
     *
     * @param string $messageId
     * @return self
     */
    public function setMessageId($messageId)
    {
        $this->message_id = $messageId;
        return $this;
    }

    /**
     * Get messageId
     *
     * @return string $messageId
     */
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * Set opened
     *
     * @param boolean $opened
     * @return self
     */
    public function setOpened($opened)
    {
        $this->opened = $opened;
        return $this;
    }

    /**
     * Get opened
     *
     * @return boolean $opened
     */
    public function getOpened()
    {
        return $this->opened;
    }

    /**
     * Return from_user id
     * @return [type] [description]
     */
    public function getFromUserDrawback()
    {
        if (!is_null($this->getFromUser())) {
            return $this->getFromUser()->getId();
        }
    }

    /**
     * Return to_user id
     * @return [type] [description]
     */
    public function getToUserDrawback()
    {
        if (!is_null($this->getToUser())) {
            return $this->getToUser()->getId();
        }
    }
}
