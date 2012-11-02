<?php

namespace Jordo\ReportBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jordo\ReportBundle\Entity\ReportCa;
use Jordo\ReportBundle\Form\ReportCaType;

/**
 * ReportCa controller.
 *
 * @Route("/report/ca")
 */
class ReportCaController extends Controller
{
    /**
     * Lists all ReportCa entities.
     *
     * @Route("/", name="report_ca")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoReportBundle:ReportCa')->findAll();
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new ReportCa entity.
     *
     * @Route("/", name="report_ca_create")
     * @Method("POST")
     * @Template("JordoReportBundle:ReportCa:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new ReportCa();
        $form = $this->createForm(new ReportCaType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('report_ca_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new ReportCa entity.
     *
     * @Route("/new", name="report_ca_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ReportCa();
        $form   = $this->createForm(new ReportCaType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ReportCa entity.
     *
     * @Route("/{id}", name="report_ca_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoReportBundle:ReportCa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReportCa entity.');
        }

        $projects = $em->getRepository('JordoProjectBundle:Project')->createQueryBuilder('p')
                       ->orderBy('p.id')
                       ->where('p.state IN(:state)')
                       ->setParameter('state', array('devis', 'current'))
                       ->getQuery()
                       ->getResult();


        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'projects'    => $projects,
        );
    }

    /**
     * Displays a form to edit an existing ReportCa entity.
     *
     * @Route("/{id}/edit", name="report_ca_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoReportBundle:ReportCa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReportCa entity.');
        }

        $editForm = $this->createForm(new ReportCaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing ReportCa entity.
     *
     * @Route("/{id}", name="report_ca_update")
     * @Method("PUT")
     * @Template("JordoReportBundle:ReportCa:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoReportBundle:ReportCa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReportCa entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ReportCaType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('report_ca_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ReportCa entity.
     *
     * @Route("/{id}", name="report_ca_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoReportBundle:ReportCa')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ReportCa entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('report_ca'));
    }

    /**
     * Creates a form to delete a ReportCa entity by id.
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
