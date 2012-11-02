<?php

namespace Jordo\CalendarBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\CalendarBundle\Entity\Event;
use Jordo\CalendarBundle\Entity\Guest;
use Jordo\CalendarBundle\Form\EventType;

/**
 * Event controller.
 *
 * @Route("/calendar/event")
 */
class EventController extends Controller
{
    static public function notifyWrite($sc, $action, $entity)
    {
        $who = sprintf(
            '<a href="%s">%s</a>',
            $entity->getUser() ? $sc->get('router')->generate('user_show', array('id' => $entity->getUser()->getId())) : '#',
            $entity->getUser() ?: 'Anonymous'
        );

        $event = sprintf(
            '<a href="%s">%s</a>',
            $sc->get('router')->generate('calendar_event_show', array('id' => $entity->getParam1()->getId())),
            $entity->getParam1()
        );

        $date = $entity->getParam1()->getDateStart()->format('d/m/Y H\hi');
           
        // jordo.contact.event.$ACTION
        // create = %s a ajouté un évènement (%s)
        // update = %s a modifé une prise de contact avvec %s (par %s)
        switch( $action )
        {
            case 'update' : return sprintf('%s a modifié l\'événement (%s) du %s', $who, $event, $date); break;
            case 'create' : return sprintf('%s a ajouté un évènement (%s) pour le %s', $who, $event, $date); break;
        }
    }

    /**
     * Lists all Event entities.
     *
     * @Route("/", name="calendar_event")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoCalendarBundle:Event')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * @Route("/subscribe-new-{event}-{user}/{response}", name="calendar_event_subscribe_new", defaults={"user"=0, "response"=1})
     * @Method("GET")
     * @Template()
     */
    public function subscribeNewAction($event, $user, $response)
    {
        // @TODO {user} subscribe
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository('JordoCalendarBundle:Event')->find($event);

        # User
        if( $user )
            $user = $em->getRepository('JordoUserBundle:User')->find($user);
        else
            $user = $this->get('security.context')->getToken()->getUser();

        # Guest
        $guest = new Guest();
        $guest -> setUser($user);
        $guest -> setEvent($event);
        $guest -> setResponse($response);

        $event -> addGuest($guest);

        $em->persist($guest);
        $em->persist($event);
        $em->flush();

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/subscribe-{guest}/{response}", name="calendar_event_subscribe", defaults={"response"=1})
     * @Method("GET")
     * @Template()
     */
    public function subscribeAction($guest, $response)
    {
        $em = $this->getDoctrine()->getManager();

        # Guest
        $guest = $em->getRepository('JordoCalendarBundle:Guest')->find($guest);
        $guest -> setResponse($response);
        $guest -> setDateUpdated(new \DateTime());

        $em->persist($guest);
        $em->flush();

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * Creates a new Event entity.
     *
     * @Route("/", name="calendar_event_create")
     * @Method("POST")
     * @Template("JordoCalendarBundle:Event:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Event();
        $entity->setAddedBy($this->get('security.context')->getToken()->getUser());

        $form = $this->createForm(new EventType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('Thiktak.core.notify')->log($entity);

            return $this->redirect($this->generateUrl('calendar_event_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Event entity.
     *
     * @Route("/new", name="calendar_event_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Event();
        $form   = $this->createForm(new EventType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}", name="calendar_event_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoCalendarBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/{id}/edit", name="calendar_event_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoCalendarBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $editForm = $this->createForm(new EventType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Event entity.
     *
     * @Route("/{id}", name="calendar_event_update")
     * @Method("PUT")
     * @Template("JordoCalendarBundle:Event:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoCalendarBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EventType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('Thiktak.core.notify')->log($entity);

            return $this->redirect($this->generateUrl('calendar_event_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}", name="calendar_event_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoCalendarBundle:Event')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Event entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('calendar_event'));
    }

    /**
     * Creates a form to delete a Event entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
