<?php

namespace Jordo\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class TodoController extends Controller
{
    /**
     * @Route("/calendar/todo", name="calendar_todo")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoCalendarBundle:Event')
                       ->createQueryBuilder('e')
                       ->where('e.isTodo = true')
                       ->getQuery()->getResult();

        return compact('entities');
    }
}
