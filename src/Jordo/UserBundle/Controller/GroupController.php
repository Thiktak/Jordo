<?php

namespace Jordo\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\UserBundle\Entity\Group;
use Jordo\UserBundle\Form\GroupType;

/**
 * Group controller.
 *
 * @Route("/group")
 */
class GroupController extends Controller
{
    /**
     * Lists all Group entities.
     *
     * @Route("/", name="group")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_GROUP_LIST, ROLE_ADMIN")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoUserBundle:Group')->createQueryBuilder('g')
            ->orderBY('g.name')
            ->getQuery()
            ->getResult();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Group entity.
     *
     * @Route("/", name="group_create")
     * @Method("POST")
     * @Secure(roles="ROLE_JORDO_USER_GROUP_ADMIN, ROLE_ADMIN")
     * @Template("JordoUserBundle:Group:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Group();
        $form = $this->createForm(new GroupType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('group_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Group entity.
     *
     * @Route("/join/{user}/{group}/{value}", name="group_join", defaults={"value"=1})
     * @Secure(roles="ROLE_JORDO_USER_GROUP_ADMIN, ROLE_ADMIN")
     * @Method("GET")
     */
    public function joinAction(Request $request, $user, $group)
    {
        $em = $this->getDoctrine()->getManager();

        $user  = $em->getRepository('JordoUserBundle:User')->find($user);
        $group = $em->getRepository('JordoUserBundle:Group')->find($group);

        if ( !$user )   throw $this->createNotFoundException('Unable to find User entity.');
        if ( !$group )  throw $this->createNotFoundException('Unable to find Group entity.');

        $user->addGroup($group);
        $em->persist($user);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Displays a form to create a new Group entity.
     *
     * @Route("/new", name="group_new")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_GROUP_ADMIN, ROLE_ADMIN")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Group();
        $form   = $this->createForm(new GroupType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Group entity.
     *
     * @Route("/{id}", name="group_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoUserBundle:Group')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Group entity.
     *
     * @Route("/{id}/edit", name="group_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_GROUP_ADMIN, ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoUserBundle:Group')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }

        $editForm = $this->createForm(new GroupType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Group entity.
     *
     * @Route("/{id}", name="group_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_JORDO_USER_GROUP_ADMIN, ROLE_ADMIN")
     * @Template("JordoUserBundle:Group:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoUserBundle:Group')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new GroupType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('group_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Group entity.
     *
     * @Route("/{id}", name="group_delete")
     * @Method("DELETE")
     * @Secure(roles="ROLE_JORDO_USER_GROUP_ADMIN, ROLE_ADMIN")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoUserBundle:Group')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Group entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('group'));
    }

    /**
     * Creates a form to delete a Group entity by id.
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
