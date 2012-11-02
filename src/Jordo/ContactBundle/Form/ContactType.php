<?php

namespace Jordo\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fname')
            ->add('lname')
            ->add('type')
            ->add('firm')
            ->add('addr')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\ContactBundle\Entity\Contact'
        ));
    }

    public function getName()
    {
        return 'jordo_contactbundle_contacttype';
    }
}
