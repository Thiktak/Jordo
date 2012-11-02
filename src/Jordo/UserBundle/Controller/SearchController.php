<?php

namespace Jordo\UserBundle\Controller;

use Thiktak\SearchBundle\Base\BaseSearch;

class SearchController extends BaseSearch
{
    public function execute()
    {
        return array(
            'name'    => 'user.search.name',
            'type'    => 'user',
            'icon'    => 'icon-user',
            'entity'  => 'Jordo\UserBundle\Entity\User',
            'render'  => 'JordoUserBundle:User:search.html.twig',
            /*'prepare' => function($entity)
            {
                return $entity->leftjoin('x.infos', 'i');
            },*/
            'search'  => function($entity, $word, $param)
            {
                return $entity->andWhere(
                    'x.fname LIKE :' . $param . ' OR ' .
                    'x.lname LIKE :' . $param . ' OR ' .
                    'x.addrPerso LIKE :' . $param . ' OR ' .
                    'x.phone LIKE :' . $param . ' OR ' .
                    'x.email LIKE :' . $param
                )->setParameter($param, '%' . $word . '%');
            }
        );
    }
}
