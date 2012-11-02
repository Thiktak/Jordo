<?php

namespace Jordo\ContactBundle\Controller;

use Thiktak\SearchBundle\Base\BaseSearch;

class SearchController extends BaseSearch
{
    public function execute()
    {
        return array(
            'name'    => 'contact.search.name',
            'type'    => 'contact',
            'icon'    => 'icon-user',
            'entity'  => 'Jordo\ContactBundle\Entity\Contact',
            'render'  => 'JordoContactBundle:Contact:search.html.twig',
            'prepare' => function($entity)
            {
                return $entity->leftjoin('x.infos', 'i');
            },
            'search'  => function($entity, $word, $param)
            {
                return $entity->andWhere(
                    'x.fname LIKE :' . $param . ' OR ' .
                    'x.lname LIKE :' . $param . ' OR ' .
                    'x.firm LIKE :' . $param . ' OR ' .
                    'x.addr LIKE :' . $param . ' OR ' .
                    'i.value LIKE :' . $param
                )->setParameter($param, '%' . $word . '%');
            }
        );
    }
}
