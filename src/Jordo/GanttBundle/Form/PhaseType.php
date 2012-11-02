<?php

namespace Jordo\GanttBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iorder', 'choice', array(
                'choices' => array_combine($val = range(1, 10), $val),
            ))
            ->add('title')
            ->add('numberDaysAfter')
            ->add('numberDays')
            ->add('description')
            ->add('price')
            ->add('numberJeh')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\GanttBundle\Entity\Phase'
        ));
    }

    public function getName()
    {
        return 'jordo_ganttbundle_phasetype';
    }
}
