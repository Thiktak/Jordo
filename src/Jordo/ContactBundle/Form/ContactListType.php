<?php

namespace Jordo\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactListType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contact', 'entity', array(
                'attr' => array(
                    'data-autocomplete' => true
                ),
                'empty_value' => '',
                'class' => 'Jordo\ContactBundle\Entity\Contact',
                'query_builder' => function ($repository) {
                    return $repository
                            ->createQueryBuilder('c');
                }
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    public function getName()
    {
        return null; //'jordo_contactbundle_contactlisttype';
    }
}
