<?php

namespace Jordo\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\ProjectBundle\Entity\Document;
use Jordo\ProjectBundle\Form\DocumentType;

/**
 * Document controller.
 *
 * @Route("/project/document")
 */
class DocumentController extends Controller
{
    /**
     * Lists all Document entities.
     *
     * @Route("/", name="project_document")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoProjectBundle:Document')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all Document entities.
     *
     * @Route("/widget/{project}", name="project_document_widget")
     * @Method("GET")
     * @Template("JordoProjectBundle:Document:widget.html.twig")
     */
    public function widgetAction($project)
    {
        $types2 = array();
        $em = $this->getDoctrine()->getManager();
        $project  = $em->getRepository('JordoProjectBundle:Project')->find($project);
        $types    = $em->getRepository('JordoProjectBundle:DocumentType')->findAll();
        $entities = $project->getDocuments();


        foreach( $entities as $entity )
            $types2[$entity->getDoctype()->getId()]['children'][] = $entity;

        foreach( $types as $i => $type ) {
            $i = $type->getId();
            $types2[$i]['type']     = $type;
            if( !isset($types2[$i]['children']) )
                $types2[$i]['children'] = array();

            $nb = $type->getIsNumberofIntervenants() ? count($project->getTeam()) : 1;
            for( $j = count($types2[$i]['children']) ; $j < $nb ; $j++ ) // getTeam(true) : isIntervenant
                $types2[$i]['children'][] = null;
        }

        $nbdoctypes = 0;
        foreach( $types as $doc )
            $nbdoctypes += $doc->getIsNumberofIntervenants() ? count($project->getTeam()): 1;

        return array(
            'nbdoctypes'  => $nbdoctypes,
            'project'  => $project,
            'types'    => $types2,
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Document entity.
     *
     * @Route("/", name="project_document_create")
     * @Method("POST")
     * @Template("JordoProjectBundle:Document:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Document();
        $entity->setUser($this->get('security.context')->getToken()->getUser());
        
        $form = $this->createForm(new DocumentType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('project_document_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Document entity.
     *
     * @Route("/new", name="project_document_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Document();
        $this->get('thiktak.core.form')->bind($entity);
        $form   = $this->createForm(new DocumentType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Document entity.
     *
     * @Route("/{id}", name="project_document_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $revisions = $em->getRepository('JordoDocumentBundle:Document')->findBy(array(
            'path'  => $entity->getDocument()->getPath(),
            'title' => $entity->getDocument()->getTitle(),
        ), array('revision' => 'DESC'));

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'revisions'   => $revisions,
        );
    }

    /**
     * Displays a form to edit an existing Document entity.
     *
     * @Route("/{id}/edit", name="project_document_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $editForm = $this->createForm(new DocumentType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Document entity.
     *
     * @Route("/{id}", name="project_document_update")
     * @Method("PUT")
     * @Template("JordoProjectBundle:Document:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DocumentType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('project_document_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Document entity.
     *
     * @Route("/{id}", name="project_document_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoProjectBundle:Document')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Document entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project_document'));
    }

    /**
     * Creates a form to delete a Document entity by id.
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
