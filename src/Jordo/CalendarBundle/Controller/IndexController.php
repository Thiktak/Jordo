<?php

namespace Jordo\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class IndexController extends Controller
{
    protected function getClass($entity)
    {
        $entity = is_object($entity) ? get_class($entity) : $entity;

        $class = explode('\\', str_ireplace(array('bundle', 'entity'), '', $entity));
        $class = array_slice($class, -3);
        $class = preg_replace('`-{1,}`', '-', implode('-', $class));

        return $class;
    }

    protected function getSources()
    {
        $entities   = array();
        $entities['calendar-event'] = array(
          'id'    => 'calendar-event',
          'sql'   => 'JordoCalendarBundle:Event',
          'label' => 'info',
          'state' => 1,
          'color' => 'red',
        );
        $entities['contact-call'] = array(
          'id'    => 'contact-call',
          'sql'   => 'JordoContactBundle:Call',
          'label' => 'warning',
          'state' => 1,
          'color' => 'green',
        );

        foreach( $entities as $key => $val )
          $entities[$key]['state'] = $this->get('session')->get('calendars/' . $val['id'], true);

        return $entities;
    }

    /**
     * @Route("/calendar/{id}/show", name="calendar_show")
     * @Template()
     */
    public function showAction($id)
    {
        return array();
    }


    /**
     * @Route("/calendar", name="calendar")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/calendar/menu", name="calendar_menu")
     * @Template()
     */
    public function menuAction()
    {
        return array('entities' => $this->getSources());
    }

    /**
     * @Route("/calendar/{type}:{filter}", name="calendar_filter")
     * @Route("/calendar/calendars:{filter}", name="calendar_filter_toggle", defaults={"type"="calendars"})
     */
    public function filterAction($type = 'calendars', $filter)
    {
        if( !empty($filter) )
          $this->get('session')->set(
            $type . '/' . $filter,
            !$this->get('session')->get($type . '/' . $filter, 1)
          );

        return $this->redirect($this->generateUrl('calendar'));
    }

    /**
     * @Route("/calendar/sources", name="calendar_sources", defaults={"_format"="json"})
     */
    public function sourcesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sources = array();

        $entities = $em->getRepository('JordoCalendarBundle:Event')
                       ->createQueryBuilder('e');

        foreach( $this->getSources() as $source )
          if( $source['state'] )
            $entities = $entities->orWhere('e INSTANCE OF ' . $source['sql']);

        $entities = $entities->getQuery()->getResult();

        $i = 0;
        foreach( $entities as $entity ) {
          $class = $this->getClass($entity);

          $sources[$i] = array(
            'id'     => $entity->getId(),
            'title'  => trim(($entity->getTitle() ?: ' ') . ' - ' . ((string) $entity), ' -'),
            'start'  => $entity->getDateStart()->format('U'),
            'className'  => 'event-' . strtolower($class),
            'allDay' => !$entity->getDateEnd(),
            'url'    => $this->generateUrl('calendar_event_show', array('id' => $entity->getId()))
          );

          if( !$sources[$i]['allDay'] )
            $sources[$i]['end'] = $entity->getDateEnd() ? $entity->getDateEnd()->format('U') : null;
            
          $i++;
        }

        exit(json_encode($sources));
    }


}
