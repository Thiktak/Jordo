<?php

namespace Jordo\ReportBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jordo\ReportBundle\Entity\ReportItem;
use Jordo\ReportBundle\Form\ReportItemType;

/**
 * ReportItem controller.
 *
 * @Route("/report/item")
 */
class ReportItemController extends Controller
{
    /**
     * @Route("/widget/{object}/{id}", name="report_item_widget")
     * @Method("GET")
     * @Template()
     */
    public function widgetAction($object, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoReportBundle:ReportItem')->findBy(array('object' => $object, 'objectId' => $id));

        return array(
            'entities' => $entities,
            'object'   => $object,
            'id'       => $id,
            'guid'     => uniqid(),
        );
    }


    /**
     * @Route("/save/{id}/{name}", name="report_item_save")
     * @Method("GET")
     * @Template()
     */
    public function saveAction($id, $name)
    {
        $em = $this->getDoctrine()->getManager();

        $value = preg_replace('`(<br>){0,}$`', '', $this->getRequest()->get('value'));

        $entity = $em->getRepository('JordoReportBundle:ReportItem')->find($id);


        switch($name)
        {
            case 'comment' :
                if( $value !== null ) {
                    $entity->{'set' . $name}(trim($value));

                    $em->persist($entity);
                    $em->flush();

                    echo $entity->{'get' . $name}();
                }
                break;

            case 'checked' :

                if( $value !== null ) {
                    $entity->setChecked( !$entity->getChecked() );
                    $em->persist($entity);
                    $em->flush();
                }

                echo $entity->getChecked() ? '<i class="icon-ok"></i>' : '<i class="icon-ok icon-white"></i>';
                break;

            case '_new' :

                if( $value !== null ) {
                    $entity->setChecked( !$entity->getChecked() );
                    $em->persist($entity);
                    $em->flush();
                }

                echo $entity->getChecked() ? '<i class="icon-ok"></i>' : '<i class="icon-ok icon-white"></i>';
                break;
        }
        exit();
    }

    /**
     * Lists all ReportItem entities.
     *
     * @Route("/", name="report_item")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoReportBundle:ReportItem')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new ReportItem entity.
     *
     * @Route("/", name="report_item_create")
     * @Method("POST")
     * @Template("JordoReportBundle:ReportItem:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new ReportItem();
        $form = $this->createForm(new ReportItemType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('report_item_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new ReportItem entity.
     *
     * @Route("/new", name="report_item_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ReportItem();
        $form   = $this->createForm(new ReportItemType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ReportItem entity.
     *
     * @Route("/{id}", name="report_item_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoReportBundle:ReportItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReportItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ReportItem entity.
     *
     * @Route("/{id}/edit", name="report_item_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoReportBundle:ReportItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReportItem entity.');
        }

        $editForm = $this->createForm(new ReportItemType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing ReportItem entity.
     *
     * @Route("/{id}", name="report_item_update")
     * @Method("PUT")
     * @Template("JordoReportBundle:ReportItem:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoReportBundle:ReportItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ReportItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ReportItemType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('report_item_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ReportItem entity.
     *
     * @Route("/{id}", name="report_item_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoReportBundle:ReportItem')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ReportItem entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('report_item'));
    }

    /**
     * Creates a form to delete a ReportItem entity by id.
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
