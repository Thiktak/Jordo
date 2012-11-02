<?php

namespace Jordo\ContactBundle\Controller;

use Jordo\StatsBundle\Base\BaseStatistique;


Class StatistiquesController extends BaseStatistique
{
    static public function defineAxisNofContacts()
    {
        return array(
          'entity' => 'Jordo\ContactBundle\Entity\Contact',
          'val'    => 'COUNT(x.id)',
          'key'    => 'x.dateCreated',
          //'where'  => 'x.dateStart IS NOT NULL', 
        );
    }

    static public function defineAxisNofContactsByType()
    {
        return array(
          'entity' => 'Jordo\ContactBundle\Entity\Contact',
          'val'    => array('t.title', 'COUNT(x.id)'),
          'key'    => 'x.dateCreated',
          'join'   => 'x.type as t',
          //'where'  => 'x.dateStart IS NOT NULL', 
        );
    }

    static public function defineAxisNofCalls()
    {
        return array(
          'entity' => 'Jordo\ContactBundle\Entity\Call',
          'val'    => 'COUNT(x.id)',
          'key'    => 'x.dateCreated',
          //'where'  => 'x.dateStart IS NOT NULL', 
        );
    }
}