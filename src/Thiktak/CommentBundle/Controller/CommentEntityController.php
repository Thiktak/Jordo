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
 * @Route("/comment-entity")
 */
class CommentEntityController extends Controller
{

    /**
     * Lists all Comment entities.
     *
     * @Route("/widget/{object}/{id}", name="commententity_widget")
     * @Method("GET")
     * @Template()
     */
    public function widgetAction($object, $id)
    {
        $uid = uniqid();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository($object)->find($id);
        $entities = $entities->getComments();


        return compact('entities', 'object', 'id', 'uid');
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/{object}/{id}", name="commententity_create")
     * @Method("POST")
     * @Template("ThiktakCommentBundle:Comment:new.html.twig")
     */
    public function createAction(Request $request, $object, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity  = new Comment();
        $entity -> setUser($this->get('security.context')->getToken()->getUser());

        $_object = $em->getRepository($object)->find($id);

        $form = $this->createForm(new CommentType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);

            $_object -> addComment($entity);
            $em->persist($_object);

            $em->flush();

            return $this->redirect($request->headers->get('referer'));
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
     * @Route("/new/{object}/{id}", name="commententity_new")
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
     * @Route("/{id}", name="commententity_show")
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
     * @Route("/{id}/edit", name="commententity_edit")
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
     * @Route("/{id}", name="commententity_update")
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

            return $this->redirect($this->generateUrl('commententity_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
}
