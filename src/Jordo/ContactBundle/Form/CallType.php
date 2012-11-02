<?php

namespace Jordo\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CallType extends AbstractType
{
    protected $contact;

    public function __construct($contact = null)
    {
        $this->contact = $contact;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = $this->contact;

        $builder
            ->add('dateStart')
            ->add('dateEnd')
            ->add('title', 'choice', array(
                'choices' => array(
                    'PRO' => 'Prospection',
                    'COM' => 'Echange',
                    'RDV' => 'Rendez-vous',
                )
            ))
            ->add('description')
            ->add('isTodo')
            ->add('info', 'entity', array(
                'class' => 'Jordo\ContactBundle\Entity\Info',
                //'property'=>'status_name',
                'query_builder' => function ($repository) use($contact) {
                    $return = $repository->createQueryBuilder('i');

                    if( $contact )
                        $return = $return->where('i.contact = ?1')->setParameter(1, $contact);
                    return $return;
                }
            ))
            ->add('dateCallback'/*, 'choice', array(
                'choices' => array(
                    1      => 'Demain',
                    7      => 'La semaine prochaine',
                    7*2    => 'Dans deux semaines',
                    7*4    => 'Dans un mois (4sem)',
                    2*30.5 => 'Dans deux mois',
                    6*30.5 => 'Dans six mois',
                    0      => 'Jamais',
                )
            )*/)
            ->add('state', 'choice', array(
                'choices' => array(
                    'pass'   => 'Passé',
                    'd'      => 'Décroché',
                    'nd'     => 'Non décroché',
                    'dead'   => 'Mort',
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jordo\ContactBundle\Entity\Call'
        ));
    }

    public function getName()
    {
        return 'jordo_contactbundle_calltype';
    }
}
