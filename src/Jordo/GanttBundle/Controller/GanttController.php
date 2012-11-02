<?php

namespace Jordo\GanttBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\GanttBundle\Entity\Gantt;
use Jordo\GanttBundle\Form\GanttType;

/**
 * Gantt controller.
 *
 * @Route("/gantt")
 */
class GanttController extends Controller
{
    /**
     * Lists all Gantt entities.
     *
     * @Route("/", name="gantt")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoGanttBundle:Gantt')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Gantt entity.
     *
     * @Route("/", name="gantt_create")
     * @Method("POST")
     * @Template("JordoGanttBundle:Gantt:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Gantt();
        $form = $this->createForm(new GanttType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gantt_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Gantt entity.
     *
     * @Route("/new", name="gantt_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Gantt();
        $form   = $this->createForm(new GanttType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Gantt entity.
     *
     * @Route("/{id}", name="gantt_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoGanttBundle:Gantt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gantt entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $maxDays = array();
        $totalJEH = 0;
        $totalPrice = 0;
        foreach( $entity->getPhases() as $phase ) {
            $maxDays[]   = $phase->getNumberDays() + $phase->getNumberDaysAfter();
            $totalJEH   += $phase->getNumberJeh();
            $totalPrice += $phase->getNumberJeh() * $phase->getPrice();
        }

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'totalPrice'  => $totalPrice,
            'maxDays'     => $maxDays ? max($maxDays) : 0,
        );
    }

    /**
     * Finds and displays a Gantt entity.
     *
     * @Route("/widget/{id}", name="gantt_widget")
     * @Method("GET")
     * @Template("JordoGanttBundle:Gantt:widget.html.twig")
     */
    public function widgetAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoGanttBundle:Gantt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gantt entity.');
        }

        $maxDays = array();
        $totalJEH = 0;
        $totalPrice = 0;
        foreach( $entity->getPhases() as $phase ) {
            $maxDays[]   = $phase->getNumberDays() + $phase->getNumberDaysAfter();
        }

        return array(
            'entity'      => $entity,
            'maxDays'     => $maxDays ? max($maxDays) : 0,
        );
    }

    /**
     * Displays a form to edit an existing Gantt entity.
     *
     * @Route("/{id}/edit", name="gantt_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoGanttBundle:Gantt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gantt entity.');
        }

        $editForm = $this->createForm(new GanttType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Gantt entity.
     *
     * @Route("/{id}", name="gantt_update")
     * @Method("PUT")
     * @Template("JordoGanttBundle:Gantt:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoGanttBundle:Gantt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gantt entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new GanttType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gantt_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Gantt entity.
     *
     * @Route("/{id}", name="gantt_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoGanttBundle:Gantt')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Gantt entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gantt'));
    }

    /**
     * Creates a form to delete a Gantt entity by id.
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
