<?php

namespace Jordo\ReportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReportCaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('dateStart')
            ->add('dateEnd')
            ->add('isTodo')
            ->add('addedBy')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\ReportBundle\Entity\ReportCa'
        ));
    }

    public function getName()
    {
        return 'jordo_reportbundle_reportcatype';
    }
}
