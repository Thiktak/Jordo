<?php

namespace Thiktak\CommentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Thiktak\CommentBundle\Entity\Comment;
use Thiktak\CommentBundle\Form\CommentType;

/**
 * Comment controller.
 *
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * Lists all Comment entities.
     *
     * @Route("/", name="comment")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ThiktakCommentBundle:Comment')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all Comment entities.
     *
     * @Route("/widget/{object}/{id}", name="comment_widget")
     * @Method("GET")
     * @Template()
     */
    public function widgetAction($object, $id)
    {
        $uid = uniqid();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ThiktakCommentBundle:Comment')->findBy(
            array('type' => $object, 'typeId' => $id),
            array('dateCreated' => 'DESC')
        );

        return compact('entities', 'object', 'id', 'uid');
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/{object}/{id}", name="comment_create")
     * @Method("POST")
     * @Template("ThiktakCommentBundle:Comment:new.html.twig")
     */
    public function createAction(Request $request, $object, $id)
    {
        $entity  = new Comment();
        $entity -> setType($object);
        $entity -> setTypeId($id);
        $entity -> setUser($this->get('security.context')->getToken()->getUser());

        $form = $this->createForm(new CommentType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
            //$this->generateUrl('comment_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'object' => $object,
            'id'     => $id,
        );
    }

    /**
     * Displays a form to create a new Comment entity.
     *
     * @Route("/new/{object}/{id}", name="comment_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($object, $id)
    {
        $entity = new Comment();
        $form   = $this->createForm(new CommentType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'object' => $object,
            'id'     => $id,
        );
    }

    /**
     * Finds and displays a Comment entity.
     *
     * @Route("/{id}", name="comment_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ThiktakCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Comment entity.
     *
     * @Route("/{id}/edit", name="comment_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ThiktakCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $editForm = $this->createForm(new CommentType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Comment entity.
     *
     * @Route("/{id}", name="comment_update")
     * @Method("PUT")
     * @Template("ThiktakCommentBundle:Comment:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ThiktakCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CommentType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('comment_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Comment entity.
     *
     * @Route("/{id}", name="comment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ThiktakCommentBundle:Comment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Comment entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('comment'));
    }

    /**
     * Creates a form to delete a Comment entity by id.
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
