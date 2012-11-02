<?php

namespace Jordo\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project')
            ->add('title')
            ->add('dateAdded')
            ->add('path')
            ->add('doctype')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\ProjectBundle\Entity\Document'
        ));
    }

    public function getName()
    {
        return 'jordo_projectbundle_documenttype';
    }
}
