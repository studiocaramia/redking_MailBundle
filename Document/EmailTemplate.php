<?php

namespace Redking\Bundle\MailBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Redking\Bundle\ODMTranslatorBundle\Mapping\Annotation\Translatable;

use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * EmailTemplate
 *
 * @MongoDB\Document(collection="email_template", repositoryClass="Redking\Bundle\MailBundle\Document\Repository\EmailTemplateRepository")
 */
class EmailTemplate
{

    const NAME_USER_MESSAGE = 'user_message';

    /**
     * @var string
     *
     * @MongoDB\Id(strategy="NONE")
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var date $created
     *
     * @MongoDB\Date
     * @Gedmo\Timestampable(on="create")
     */
    protected $created_at;

    /**
     * @var date $updated
     *
     * @MongoDB\Date
     * @Gedmo\Timestampable
     */
    protected $updated_at;

    /**
     * @var string $subject
     *
     * @MongoDB\String
     * @Translatable
     */
    protected $subject;

    /**
     * @var string $body
     *
     * @MongoDB\String
     * @Translatable
     */
    protected $body;

    use \Redking\Bundle\ODMTranslatorBundle\Document\TranslatableTrait;

    /**
     * @var TwigEngine
     */
    protected $twig_engine = null;

    public static function getNameChoices()
    {
        return [
            self::NAME_USER_MESSAGE => 'form.label_name_'.self::NAME_USER_MESSAGE,
        ];
    }
    
    public static function getNameChoicesValidation()
    {
        return array_keys(static::getNameChoices());
    }

    public function getNameToString()
    {
        $names = static::getNameChoices();
        return (isset($names[$this->getName()])) ? $names[$this->getName()] : '';
    }

    /**
     * Set name
     *
     * @param custom_id $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return custom_id $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdAt
     *
     * @param date $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updatedAt
     *
     * @param date $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return date $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
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
     * Set body
     *
     * @param string $body
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return string $body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * [setTwigEngine description]
     * @param TwigEngine $twig_engine [description]
     */
    public function setTwigEngine(TwigEngine $twig_engine)
    {
        $this->twig_engine = $twig_engine;
        return $this;
    }

    /**
     * [getTwigEngine description]
     * @return [type] [description]
     */
    public function getTwigEngine()
    {
        return $this->twig_engine;
    }

    /**
     * [__toString description]
     * @return string [description]
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * [getTwigSubject description]
     * @return [type] [description]
     */
    public function getTwigSubject()
    {
        return $this->convertToTwig($this->getSubject());
    }

    /**
     * [getTwigSubject description]
     * @return [type] [description]
     */
    public function getTwigBody()
    {
        return $this->convertToTwig($this->getBody());
    }

    /**
     * [convertToTwig description]
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    protected function convertToTwig($str)
    {
        return str_replace(
            ['[[', ']]', "\r\n"],
            ['{{', '}}', "\n"],
            $str);
    }

    /**
     * [applyTemplate description]
     * @param  [type] $string [description]
     * @param  [type] $vars   [description]
     * @return [type]         [description]
     */
    protected function applyTemplate($string, $vars)
    {
        if (is_null($this->getTwigEngine())) {
            throw new \Exception("Twig Engine must be attached", 1);
        }

        return $this->getTwigEngine()->render($string, $vars);
    }

    /**
     * [getFinalBody description]
     * @param  array
     * @return [type]                [description]
     */
    public function getFinalBody($vars) 
    {
        return $this->applyTemplate($this->getTwigBody(), $vars);
    }

    /**
     * [getFinalBody description]
     * @param  array
     * @return [type]                [description]
     */
    public function getFinalSubject($vars) 
    {
        return $this->applyTemplate($this->getTwigSubject(), $vars);
    }


}
