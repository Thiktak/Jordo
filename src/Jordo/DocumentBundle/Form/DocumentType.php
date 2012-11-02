<?php

namespace Jordo\DocumentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path')
            ->add('title')
            ->add('dateAdded')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\DocumentBundle\Entity\Document'
        ));
    }

    public function getName()
    {
        return 'jordo_documentbundle_documenttype';
    }
}
