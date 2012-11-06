<?php

namespace Jordo\ContactBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\ContactBundle\Entity\Call;
use Jordo\ContactBundle\Form\CallType;
use Jordo\ContactBundle\Form\ContactListType;

/**
 * Call controller.
 *
 * @Route("/call")
 */
class CallController extends Controller
{

    static public function notifyWrite($sc, $action, $entity)
    {
        $who = sprintf(
            '<a href="%s">%s</a>',
            $entity->getUser() ? $sc->get('router')->generate('user_show', array('id' => $entity->getUser()->getId())) : '#',
            $entity->getUser() ?: 'Anonymous'
        );

        $contact = sprintf(
            '<a href="%s">%s</a>',
            $sc->get('router')->generate('contact_show', array('id' => $entity->getParam1()->getInfo()->getContact()->getId())),
            $entity->getParam1()->getInfo()->getContact()
        );

        $entityHref = sprintf(
            '<a href="%s">%s</a>',
            $sc->get('router')->generate('contact_info_show', array('id' => $entity->getParam1()->getId())),
            $entity->getParam1()->getInfo()->getType()
        );
           
        // jordo.contact.call.$ACTION
        // create = %s a contacté %s par %s
        // update = %s a modifé une prise de contact avvec %s (par %s)
        switch( $action )
        {
            case 'update' : return sprintf('%s a modifié une prise de contact avec %s par %s', $who, $contact, $entityHref); break;
            case 'create' : return sprintf('%s a contacté %s par %s', $who, $contact, $entityHref); break;
        }
    }


    /**
     * Lists all Call entities.
     *
     * @Route("/", name="call")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoContactBundle:Call')->createQueryBuilder('c')
        ->orderBy('c.dateStart', 'DESC')
        ->setMaxResults(50)
        ->getQuery()
        ->getResult();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all Call entities.
     *
     * @Route("/widget/{type}-{id}", name="call_widget")
     * @Method("GET")
     * @Template()
     */
    public function widgetAction($type, $id)
    {
        $em = $this->getDoctrine()->getManager();

        switch( $type )
        {
            case 'user':
                $entities = $em->getRepository('JordoContactBundle:Call')->findBy(array('addedBy' => $id));
                break;

            case 'contact':
                $entities = $em->getRepository('JordoContactBundle:Call')->createQueryBuilder('c')
                               ->join('c.info', 'i')
                               ->join('i.contact', 'co')
                               ->where('co.id = :id')
                               ->setParameter('id', $id)
                               ->orderBy('c.dateStart', 'DESC')
                               ->getQuery()
                               ->getResult();
                               //addedBy' => $id);
                break;

            default: die(); break;
        }

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Call entity.
     *
     * @Route("/", name="call_create")
     * @Method("POST")
     * @Template("JordoContactBundle:Call:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $contact = $request->get('contact', null);
        $em = $this->getDoctrine()->getManager();

        if( $contact ) {
            $contact = $em->getRepository('JordoContactBundle:Contact')->find($contact);
        }

        $entity  = new Call();
        $entity->setAddedBy($this->get('security.context')->getToken()->getUser());

        $form = $this->createForm(new CallType($contact), $entity);
        $form->bind($request);


        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('Thiktak.core.notify')->log($entity);

            return $this->redirect($this->generateUrl('contact_show', array('id' => $entity->getInfo()->getContact()->getId())));
        }

        $old = $contact ? $em->getRepository('JordoContactBundle:Call')->createQueryBuilder('c')->join('c.info', 'i')->join('i.contact', 'co')->andWhere('co.id = ?1')->setParameter(1, $contact->getId())->orderBy('c.dateStart', 'DESC')->getQuery()->getResult() : null;
        
        return array(
            'contact' => $contact,
            'entity' => $entity,
            'old'     => $old,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Call entity.
     *
     * @Route("/new", name="call_new")
     * @Method("GET|POST")
     * @Template()
     */
    public function newAction(Request $request, $contact = null)
    {
        $contact = $request->get('contact', null);

        $entity = new Call();
        $this->get('thiktak.core.form')->bind($entity);

        if( $contact ) {
            $form   = $this->createForm(new CallType($contact), $entity);
            $em = $this->getDoctrine()->getManager();
            $contact = $em->getRepository('JordoContactBundle:Contact')->find($contact);
        }
        else
            $form   = $this->createForm(new ContactListType(), $contact);

        $old = $contact ? $em->getRepository('JordoContactBundle:Call')->createQueryBuilder('c')->join('c.info', 'i')->join('i.contact', 'co')->andWhere('co.id = ?1')->setParameter(1, $contact->getId())->orderBy('c.dateStart', 'DESC')->getQuery()->getResult() : null;
        
        return array(
            'contact' => $contact,
            'entity'  => $entity,
            'old'     => $old,
            'form'    => $form->createView(),
        );
    }

    /**
     * Finds and displays a Call entity.
     *
     * @Route("/{id}", name="call_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoContactBundle:Call')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Call entity.');
        }

        $old = $em->getRepository('JordoContactBundle:Call')->createQueryBuilder('c')->join('c.info', 'i')->join('i.contact', 'co')->andWhere('co.id = ?1')->setParameter(1, $id)->orderBy('c.dateStart', 'DESC')->getQuery()->getResult();

        return array(
            'entity' => $entity,
            'old'    => $old,
        );
    }

    /**
     * Displays a form to edit an existing Call entity.
     *
     * @Route("/{id}/edit", name="call_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoContactBundle:Call')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Call entity.');
        }

        $editForm = $this->createForm(new CallType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Call entity.
     *
     * @Route("/{id}", name="call_update")
     * @Method("PUT")
     * @Template("JordoContactBundle:Call:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoContactBundle:Call')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Call entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CallType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {

            $this->get('Thiktak.core.notify')->log($entity);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contact_show', array('id' => $entity->getInfo()->getContact()->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Call entity.
     *
     * @Route("/{id}", name="call_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoContactBundle:Call')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Call entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('call'));
    }

    /**
     * Creates a form to delete a Call entity by id.
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
