<?php

namespace Jordo\CalendarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateStart')
            ->add('dateEnd')
            ->add('title')
            ->add('description')
            ->add('isTodo')
            ->add('isOpen')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\CalendarBundle\Entity\Event'
        ));
    }

    public function getName()
    {
        return 'jordo_calendarbundle_eventtype';
    }
}
