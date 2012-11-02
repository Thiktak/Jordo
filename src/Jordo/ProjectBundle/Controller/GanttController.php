<?php

namespace Jordo\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\ProjectBundle\Entity\Gantt;
use Jordo\ProjectBundle\Form\GanttType;

/**
 * Gantt controller.
 *
 * @Route("/project/gantt/")
 */
class GanttController extends Controller
{
    /**
     * Lists all Gantt entities.
     *
     * @Route("/", name="project_gantt_")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoProjectBundle:Gantt')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Gantt entity.
     *
     * @Route("/", name="project_gantt__create")
     * @Method("POST")
     * @Template("JordoProjectBundle:Gantt:new.html.twig")
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

            return $this->redirect($this->generateUrl('project_gantt__show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Gantt entity.
     *
     * @Route("/new", name="project_gantt__new")
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
     * @Route("/{id}", name="project_gantt__show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Gantt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gantt entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Gantt entity.
     *
     * @Route("/{id}/edit", name="project_gantt__edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Gantt')->find($id);

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
     * @Route("/{id}", name="project_gantt__update")
     * @Method("PUT")
     * @Template("JordoProjectBundle:Gantt:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Gantt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gantt entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new GanttType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('project_gantt__edit', array('id' => $id)));
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
     * @Route("/{id}", name="project_gantt__delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoProjectBundle:Gantt')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Gantt entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project_gantt_'));
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
