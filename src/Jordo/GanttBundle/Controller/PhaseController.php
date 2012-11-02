<?php

namespace Jordo\GanttBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\GanttBundle\Entity\Phase;
use Jordo\GanttBundle\Form\PhaseType;

/**
 * Phase controller.
 *
 * @Route("/gantt/phase")
 */
class PhaseController extends Controller
{
    /**
     * Lists all Phase entities.
     *
     * @Route("/", name="gantt_phase")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoGanttBundle:Phase')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Phase entity.
     *
     * @Route("/gantt-{gantt}", name="gantt_phase_create")
     * @Method("POST")
     * @Template("JordoGanttBundle:Phase:new.html.twig")
     */
    public function createAction(Request $request, $gantt)
    {
        $entity  = new Phase();
        $this->get('Thiktak.core.form')->bind($entity, array('gantt' => $gantt));

        $form = $this->createForm(new PhaseType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gantt_show', array('id' => $entity->getGantt()->getId())));
        }
        
        $gantt  = $em->getRepository('JordoGanttBundle:Gantt')->find($gantt);

        return array(
            'gantt'  => $gantt,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Phase entity.
     *
     * @Route("/new/gantt-{gantt}", name="gantt_phase_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($gantt)
    {
        $em = $this->getDoctrine()->getManager();

        $gantt  = $em->getRepository('JordoGanttBundle:Gantt')->find($gantt);

        $entity = new Phase();
        $this->get('Thiktak.core.form')->bind($entity);
        $form   = $this->createForm(new PhaseType(), $entity);

        return array(
            'gantt'  => $gantt,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Phase entity.
     *
     * @Route("/{id}", name="gantt_phase_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoGanttBundle:Phase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Phase entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Phase entity.
     *
     * @Route("/{id}/edit", name="gantt_phase_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoGanttBundle:Phase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Phase entity.');
        }

        $editForm = $this->createForm(new PhaseType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Phase entity.
     *
     * @Route("/{id}", name="gantt_phase_update")
     * @Method("PUT")
     * @Template("JordoGanttBundle:Phase:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoGanttBundle:Phase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Phase entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PhaseType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gantt_show', array('id' => $entity->getGantt()->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Phase entity.
     *
     * @Route("/{id}", name="gantt_phase_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoGanttBundle:Phase')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Phase entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gantt_phase'));
    }

    /**
     * Creates a form to delete a Phase entity by id.
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
