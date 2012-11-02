<?php

namespace Thiktak\CoreBundle\Twig;

class NotifyExtension extends \Twig_Extension
{
    protected $sc;

    public function __construct($service_container)
    {
        $this->sc = $service_container;
    }

    public function getFunctions()
    {
        return array(
            'notify' => new \Twig_Function_Method($this, 'notify'),
        );
    }

    public function notify(\Thiktak\CoreBundle\Entity\Notify $entity)
    {
        if( $entity->getObject() ) {
            list($class, $function) = explode('::', $entity->getObject());

            if( method_exists($class, 'notifyWrite') )
            {
                return (string) call_user_func(
                    array($class, 'notifyWrite'),
                    $this->sc,
                    str_replace('Action', '', $function),
                    $this->sc->get('Thiktak.core.notify')->filterParams($entity)
                );
            }

            return (string) $entity->getObject();
        }
        return null;
    }

    public function getName()
    {
        return 'notify_extension';
    }
}