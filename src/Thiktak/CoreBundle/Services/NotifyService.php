<?php

namespace Thiktak\CoreBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use Thiktak\CoreBundle\Entity\Notify;

Class NotifyService
{
    protected $em;
    protected $context;
    protected $sc;

    public function __construct(EntityManager $em, $context, $sc)
    {
        $this->em      = $em;
        $this->context = $context;
        $this->sc      = $sc;
    }

    public function log()
    {
        $backtrace = debug_backtrace(); array_shift($backtrace);

        $notifyRepository = $this->em->getRepository('ThiktakCoreBundle:Notify');

        if( !isset($backtrace[0]['function'], $backtrace[0]['class']) )
            return ;

        $entity = new Notify;
        $entity -> setObject($backtrace[0]['class'] . '::' . $backtrace[0]['function']);

        if( is_object($this->context->getToken()->getUser()) )
          $entity -> setUser($this->context->getToken()->getUser());

        foreach( func_get_args() as $i => $arg )
            $entity -> { 'setParam' . ($i+1) } (is_object($arg) ? get_class($arg) . '::' . $arg->getId() : $arg);
        
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function filterParams($entity)
    {
        foreach( range(1, 4) as $paramIndex )
        {
            if( !method_exists($entity, 'getParam' . $paramIndex) )
                continue;

            $object = $entity->{'getParam' . $paramIndex}();

            if( is_string($object) && preg_match('`::`', $object) && !preg_match('`[[:blank:]]`', $object) ) {
                list($class, $id) = explode('::', $object);
                $object = $this->sc->get('doctrine.orm.entity_manager')->getRepository($class)->find($id);
                $entity->{'setParam' . $paramIndex}($object);
            }
        }
        return $entity;
    }
}

/*
    public function getParam($i)
    {
        if( !method_exists($this, 'getParam' . $i) )
            return ;

        $object = $this->{'getParam' . $i}();

        if( is_string($object) && preg_match('`::`', $object) && !preg_match('`[[:blank:]]`', $object) ) {
            list($class, $id) = explode('::', $object);
            //$o = $this->_em->getRepository($class)->find($id);
            var_dump(self::$_em);
            return $class . '(' . $id . ')';
        }

        return $object;
    }
*/