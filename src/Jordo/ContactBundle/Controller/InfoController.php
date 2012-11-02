<?php

namespace Jordo\ContactBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\ContactBundle\Entity\Info;
use Jordo\ContactBundle\Form\InfoType;

/**
 * Info controller.
 *
 * @Route("/contact/info")
 */
class InfoController extends Controller
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
            $sc->get('router')->generate('contact_show', array('id' => $entity->getParam1()->getContact()->getId())),
            $entity->getParam1()->getContact()
        );

        $entityHref = sprintf(
            '<a href="%s">%s</a>',
            $sc->get('router')->generate('contact_info_show', array('id' => $entity->getParam1()->getId())),
            $entity->getParam1()->getType()
        );

        switch( $action )
        {
            case 'update' : return sprintf('%s a modifié une information personnelle de %s (%s)', $who, $contact, $entityHref); break;
            case 'create' : return sprintf('%s a ajouté une information personnelle de %s (%s)', $who, $contact, $entityHref); break;
        }
    }

    /**
     * Lists all Info entities.
     *
     * @Route("/", name="contact_info")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoContactBundle:Info')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Info entity.
     *
     * @Route("/", name="contact_info_create")
     * @Method("POST")
     * @Template("JordoContactBundle:Info:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Info();
        $form = $this->createForm(new InfoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('Thiktak.core.notify')->log($entity);

            return $this->redirect($this->generateUrl('contact_info_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Info entity.
     *
     * @Route("/new", name="contact_info_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Info();
        $this->get('thiktak.core.form')->bind($entity);
        $form   = $this->createForm(new InfoType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Info entity.
     *
     * @Route("/{id}", name="contact_info_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoContactBundle:Info')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Info entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Info entity.
     *
     * @Route("/{id}/edit", name="contact_info_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoContactBundle:Info')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Info entity.');
        }

        $editForm = $this->createForm(new InfoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Info entity.
     *
     * @Route("/{id}", name="contact_info_update")
     * @Method("PUT")
     * @Template("JordoContactBundle:Info:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoContactBundle:Info')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Info entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('Thiktak.core.notify')->log($entity);

            return $this->redirect($this->generateUrl('contact_info_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Info entity.
     *
     * @Route("/{id}", name="contact_info_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoContactBundle:Info')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Info entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contact_info'));
    }

    /**
     * Creates a form to delete a Info entity by id.
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
