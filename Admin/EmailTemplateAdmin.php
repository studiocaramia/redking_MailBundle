<?php

namespace Redking\Bundle\MailBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EmailTemplateAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            // ->add('created_at')
            // ->add('updated_at')
            // ->add('subject')
            // ->add('body')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('getNameToString', 'trans', ['catalogue' => 'RedkingMailBundle', 'identifier' => true])
            ->add('created_at', 'datetime')
            ->add('updated_at', 'datetime')
            // ->add('subject')
            // ->add('body')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $object_class = $this->getClass();
        $subject      = $this->getSubject();
        
        // Filtre des choix possibles de name sur ceux qui ne sont pas encore présent en base
        $existing_names = $this->getModelManager()->getDocumentManager($object_class)->getRepository($object_class)->getExistingNames();
        $choices = array_diff_key($object_class::getNameChoices(), array_flip($existing_names));
        $disabled = false;

        if (!is_null($subject->getName())) {
            $choices  = [$subject->getName() => 'form.label_name_'.$subject->getName()];
            $disabled = true;
        }

        $formMapper
            ->add('name', 'choice', [
                'choices'            => $choices,
                'disabled'           => $disabled,
                'translation_domain' => $this->getTranslationDomain()
            ])
            ->add('translations', 'translation', [
                'options' => [
                    'fields' => [
                        'body' => [
                            'type' => 'textarea',
                            'options' => array('attr' => array('class' => 'ckeditor'))
                        ]
                    ]
                ]
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('getNameToString', 'trans', ['catalogue' => 'RedkingMailBundle'])
            ->add('created_at', 'datetime')
            ->add('updated_at', 'datetime')
            ->add('translations', 'string', ['template' => 'RedkingODMTranslatorBundle:SonataAdmin:show_translations.html.twig'])
        ;
    }
}
