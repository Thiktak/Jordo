<?php

namespace Thiktak\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class NotifyController extends Controller
{
    /**
     * @Route("/notify/", name="notify")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ThiktakCoreBundle:Notify')->createQueryBuilder('f')
        ->orderBy('f.dateCreated', 'DESC')
        ->setMaxResults(50)
        ->getQuery()
        ->getResult();

        return compact('entities');
    }

    /**
     * @Route("/notify/widget", name="notify_widget")
     * @Template()
     */
    public function widgetAction()
    {
        return $this->indexAction();
    }
}
