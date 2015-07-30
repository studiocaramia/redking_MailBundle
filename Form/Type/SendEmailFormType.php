<?php
/**
 * Formulaire incluant les champs de base d'un envoi d'email
 */

namespace Redking\Bundle\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints;

class SendEmailFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recipient_user', 'document', array(
                'required'    => false,
                'description' => "Utilisateur destinataire",
                'class'       => $options['user_class'],
                'property'    => "id",
            ))
            ->add('subject', null, array(
                'required'    => true,
                'description' => "Sujet",
                'constraints' => [new Constraints\NotNull(), new Constraints\NotBlank()],
            ))
            ->add('body', null, array(
                'required'    => true,
                'description' => "Corps du message",
                'constraints' => [new Constraints\NotNull(), new Constraints\NotBlank()],
            ))
        ;

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'user_class'      => 'Sonata\UserBundle\Model\UserInterface',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }
}
