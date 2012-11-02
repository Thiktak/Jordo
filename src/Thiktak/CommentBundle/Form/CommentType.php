<?php

namespace Thiktak\CommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content')
            ->add('state', 'choice', array(
                'required'  => false,
                'choices'   => array(
                    'ok'      => 'ok',
                    'error'   => 'errors',
                    'restart' => 'restart',
                    'todo'    => 'todo',
                ),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Thiktak\CommentBundle\Entity\Comment'
        ));
    }

    public function getName()
    {
        return 'thiktak_commentbundle_commenttype';
    }
}
