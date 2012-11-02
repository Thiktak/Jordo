<?php

namespace Thiktak\CoreBundle\Controller;

use Jordo\StatsBundle\Base\BaseStatistique;


Class StatistiquesController extends BaseStatistique
{
    static public function defineAxisNofNotifications()
    {
        return array(
          'entity' => 'Thiktak\CoreBundle\Entity\Notify',
          'val'    => 'COUNT(x.id)',
          'key'    => 'x.dateCreated',
          //'where'  => 'x.dateStart IS NOT NULL', 
        );
    }
}