<?php

namespace Jordo\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $years = range(date('Y'), 2000);
        $years = array_combine($years, $years);

        $builder
            ->add('fname')
            ->add('lname')
            ->add('addrPerso')
            ->add('addrParents')
            ->add('phone')
            ->add('birth')
            ->add('birthPlace')
            ->add('filiere', 'choice', array('required' => false, 'choices' => array(
                'IR' => 'IR',
                'AS' => 'AS',
                'ME' => 'ME',
                'TF' => 'TF',
            )))
            ->add('promo', 'choice', array('required' => false, 'choices' => $years))
            ->add('socialNumber')
            ->add('studentNumber')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'jordo_userbundle_usertype';
    }
}
