<?php

namespace Jordo\ReportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReportItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('object')
            ->add('objectId')
            ->add('checked')
            ->add('comment')
            ->add('reportCa')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\ReportBundle\Entity\ReportItem'
        ));
    }

    public function getName()
    {
        return 'jordo_reportbundle_reportitemtype';
    }
}
