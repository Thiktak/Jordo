<?php

namespace Thiktak\CoreBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

Class OrmService
{
    protected $em;

    public function __construct(EntityManager $em)
    {
       $this->em = $em;
    }

    public function createFilter($entity, $sessionParameter)
    {
       return new OrmServiceFilter($entity, $sessionParameter);
    }
}

Class OrmServiceFilter implements \IteratorAggregate
{
    protected $entity;
    protected $sessionParameter;

    protected $registered = array();

    public function __construct(&$entity, $sessionParameter)
    {
        $this->entity = &$entity;
        $this->sessionParameter = $sessionParameter;
    }

    public function register($filter, $callback)
    {
        $this->registered[$filter] = false;

        if( $filter == $this->sessionParameter ) {
            $this->registered[$filter] = true;
            $this->entity = $callback($this->entity);
        }
    }

    public function getFilters()
    {
        return (array) $this->registered;
    }

    public function getIterator()
    {
        return new \ArrayIterator((array) $this->registered);
    }

    public function getSelected()
    {
        return $this->sessionParameter;
    }

    public function set($name, $value) {
        // session ->set($name, $value);
    }

    public function getEntity()
    {
        return $this->entity;
    }
}