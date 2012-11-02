<?php

namespace Jordo\ProjectBundle\Controller;

use Jordo\StatsBundle\Base\BaseStatistique;


Class StatistiquesController extends BaseStatistique
{
    static public function defineAxisNumberOfProjects()
    {
        return array(
          'entity' => 'Jordo\ProjectBundle\Entity\Project',
          'val'    => 'COUNT(x.id)',
          'key'    => 'x.dateStart',
          'where'  => 'x.dateStart IS NOT NULL', 
        );
    }

    static public function defineAxisNumberOfProjectsByType()
    {
        return array(
          'entity' => 'Jordo\ProjectBundle\Entity\Project',
          'val'    => array('x.state', 'COUNT(x.id)'),
          'key'    => 'x.dateStart',
          'where'  => 'x.dateStart IS NOT NULL', 
        );
    }
}