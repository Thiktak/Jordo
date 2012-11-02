<?php

namespace Jordo\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $years = range(date('Y'), 2000);
        $years = array_combine($years, $years);

        $builder
            ->add('user')
            ->add('type')
            ->add('state', 'choice', array(
                'choices' => array(
                    'ok' => 'OK',
                    'error' => 'Erreur',
                )
            ))
            ->add('year', 'choice', array('choices' => $years))
            ->add('comment')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\UserBundle\Entity\Subscription'
        ));
    }

    public function getName()
    {
        return 'jordo_userbundle_subscriptiontype';
    }
}
