<?php

namespace Jordo\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\UserBundle\Controller\UserController;
use Jordo\UserBundle\Entity\Subscription;
use Jordo\UserBundle\Form\SubscriptionType;

/**
 * Subscription controller.
 *
 * @Route("/user/subscription")
 */
class SubscriptionController extends Controller
{
    /**
     * Lists all Subscription entities.
     *
     * @Route("/", name="user_subscription")
     * @Route("/filter:{year}", name="user_subscription_year", defaults={"year" = null})
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_JORDO_USER_SUBSCRIPTION_VIEW, ROLE_ADMIN")
     */
    public function indexAction($year = null)
    {
        $current = (date('Y') + 1 + (date('m') > 8 ? -1 : 0));
        $em = $this->getDoctrine()->getManager();

        $users    = $em->getRepository('JordoUserBundle:User')->createQueryBuilder('u')->select('u');
        $entities = $em->getRepository('JordoUserBundle:Subscription')->findAll();
        $doctypes = $em->getRepository('JordoUserBundle:SubscriptionDocType')->findAll();

        UserController::applyFilters($this, $users);
        $users = $users->getQuery()->getResult();

        $years = array(date('Y'));
        foreach( $entities as $sub )
            if( $sub->getYear() )
                $years[$sub->getYear()] = $sub->getYear();

        $years = range(min($years), max($years));

        return array(
            'year'     => $year ?: $current,
            'years'    => $years,
            'current'  => $current,
            'users'    => $users,
            'entities' => $entities,
            'doctypes' => $doctypes,
        );
    }

    /**
     * Creates a new Subscription entity.
     *
     * @Route("/create", name="user_subscription_create")
     * @Method("POST")
     * @Template("JordoUserBundle:Subscription:new.html.twig")
     * @Secure(roles="ROLE_JORDO_USER_SUBSCRIPTION_ADMIN, ROLE_ADMIN")
     */
    public function createAction(Request $request)
    {
        $entity  = new Subscription();
        $form = $this->createForm(new SubscriptionType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_subscription_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Subscription entity.
     *
     * @Route("/new", name="user_subscription_new")
     * @Secure(roles="ROLE_JORDO_USER_SUBSCRIPTION_ADMIN, ROLE_ADMIN")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new Subscription();

        $this->get('thiktak.core.form')->bind($entity);
        $form   = $this->createForm(new SubscriptionType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Subscription entity.
     *
     * @Route("/{id}", name="user_subscription_show")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_SUBSCRIPTION_VIEW, ROLE_ADMIN")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoUserBundle:Subscription')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subscription entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Subscription entity.
     *
     * @Route("/{id}/edit", name="user_subscription_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_SUBSCRIPTION_ADMIN, ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoUserBundle:Subscription')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subscription entity.');
        }

        $editForm = $this->createForm(new SubscriptionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Subscription entity.
     *
     * @Route("/{id}", name="user_subscription_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_JORDO_USER_SUBSCRIPTION_ADMIN, ROLE_ADMIN")
     * @Template("JordoUserBundle:Subscription:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoUserBundle:Subscription')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subscription entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SubscriptionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_subscription_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Subscription entity.
     *
     * @Route("/{id}", name="user_subscription_delete")
     * @Secure(roles="ROLE_JORDO_USER_SUBSCRIPTION_ADMIN, ROLE_ADMIN")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoUserBundle:Subscription')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Subscription entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user_subscription'));
    }

    /**
     * Creates a form to delete a Subscription entity by id.
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
