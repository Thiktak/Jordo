<?php

namespace Jordo\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'choices' => array(
                    'tel'   => 'Téléphone',
                    'mail'  => 'Email',
                    'other' => 'Autre',
                )
            ))
            ->add('value')
            ->add('contact')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\ContactBundle\Entity\Info'
        ));
    }

    public function getName()
    {
        return 'jordo_contactbundle_infotype';
    }
}
