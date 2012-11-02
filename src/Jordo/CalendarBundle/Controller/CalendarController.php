<?php

namespace Jordo\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class CalendarController extends Controller
{
/**
     * @Route("/calendar/widget", name="calendar_widget")
     * @Template()
     */
    public function widgetAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sources = array();

        $entities = $em->getRepository('JordoCalendarBundle:Event')
                       ->createQueryBuilder('e')
                       ->orderBy('e.dateStart', 'DESC')
                       ->where('e.dateStart >= CURRENT_DATE()')
                       ->getQuery()
                       ->getResult();

        return array('entities' => $entities);
    }
}