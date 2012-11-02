<?php

namespace Jordo\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('budget')
            /*
            ->add('state', 'choice', array(
                'choices' => array(
                    'devis' => 'Devis / projet',
                    'wait' => 'En attente',
                    'current' => 'En cours',
                    'close' => 'Terminé',
                    'cancel' => 'Annulé',
                )
            ))
            */
            ->add('contact', null, array('empty_value' => '', 'attr' => array('data-autocomplete' => true)))
            ->add('description')
            ->add('contexte')
            ->add('demande')
            ->add('dateStart')
            ->add('gantt', null, array(
                'required'  => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\ProjectBundle\Entity\Project'
        ));
    }

    public function getName()
    {
        return 'jordo_projectbundle_projecttype';
    }
}
