<?php

namespace Jordo\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\UserBundle\Entity\User;
use Jordo\UserBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    static function applyFilters($t, $entities)
    {
        $em = $t->getDoctrine()->getManager();
        $years  = $em->getRepository('JordoUserBundle:Subscription')->createQueryBuilder('s')->select('DISTINCT s.year')->orderBy('s.year', 'DESC')->getQuery()->getArrayResult();
        $groups = $em->getRepository('JordoUserBundle:Group')->findAll();

        $t->current = $current = date('Y') + 1 + (date('m') > 8 ? -1 : 0);
        $t->filter_type  = $t->get('session')->get('user/type',  'all');
        $t->filter_group = $t->get('session')->get('user/group', 'all');
        $t->filter_year  = $t->get('session')->get('user/year',  'all');

        $t->filters = array();

        $t->filters['type'] = $t->get('thiktak.core.orm')->createFilter($entities, $t->filter_type);
        $t->filters['type'] -> register('all',    function($entity) { return $entity; });
        $t->filters['type'] -> register('member', function($entity) { return $entity->join('u.subscriptions', 's')->andWhere('s.id != 0'); });
        $t->filters['type'] -> register('ancien', function($entity) use($current) { return $entity->join('u.subscriptions', 's')->andWhere('s.id != 0')->andWhere('s.year < :year')->setParameter('year', $current); });
        $t->filters['type'] -> register('extern', function($entity) { return $entity->join('u.subscriptions', 's')->andHaving('COUNT(s.id) = 0'); });


        $t->filters['group'] = $t->get('thiktak.core.orm')->createFilter($entities, $t->filter_group);
        $t->filters['group'] -> register('all', function($entity) { return $entity; });
        foreach( $groups as $group ) {
            $group = $group->getName();
            $t->filters['group'] -> register($group, function($entity) use($group) { return $entity->join('u.groups', 'g2')->andWhere('g2.name = :group_id')->setParameter('group_id', $group); });
        }

        $t->filters['year'] = $t->get('thiktak.core.orm')->createFilter($entities, $t->filter_year);
        $t->filters['year'] -> register('all', function($entity) { return $entity; });
        foreach( $years as $year ) {
            $year = current($year);
            $t->filters['year'] -> register($year, function($entity) use($year) { return $entity->join('u.subscriptions', 's2')->andWhere('s2.year = :year_year')->setParameter('year_year', $year); });
        }
    }


    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_USER_LIST, ROLE_ADMIN")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $userRepository = $entities = $em->getRepository('JordoUserBundle:User');
        $doctypes = $em->getRepository('JordoUserBundle:SubscriptionDocType')->findAll();
        $entities = $userRepository->createQueryBuilder('u')->select('u')->orderBY('u.lname, u.fname');
        
        self::applyFilters($this, $entities);

        $entities = $entities->getQuery()->getResult();

        return array(
            'filters'  => $this->filters,
            'entities' => $entities,
            'doctypes' => $doctypes,
            'year'     => (date('Y') + 1 + (date('m') > 8 ? -1 : 0)),
        );
    }

    /**
     * @Route("/{type}:{filter}", name="user_filter")
     */
    public function filterAction($type, $filter)
    {
        $this->get('session')->set('user/' . $type, $filter);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * Lists all User entities.
     *
     * @Route("/menu", name="user_menu")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_USER_LIST, ROLE_ADMIN")
     * @Template()
     */
    public function menuAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('JordoUserBundle:User')->findAll(null, array('username' => 'ASC'));

        $userRepository = $entities = $em->getRepository('JordoUserBundle:User');
        $entities = $userRepository->createQueryBuilder('u')->select('u')->orderBY('u.lname, u.fname');
        
        self::applyFilters($this, $entities);

        $entities = $entities->getQuery()->getResult();

        return array(
            'filters'  => $this->filters,
            'entities' => $entities,
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/", name="user_create")
     * @Method("POST")
     * @Secure(roles="ROLE_JORDO_USER_USER_ADMIN, ROLE_ADMIN")
     * @Template("JordoUserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new User();
        $form = $this->createForm(new UserType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_USER_ADMIN, ROLE_ADMIN")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        // @TODO: @Secure(roles="ROLE_JORDO_USER_USER_LIST, ROLE_ADMIN")

        $em = $this->getDoctrine()->getManager();

        $entity   = $em->getRepository('JordoUserBundle:User')->find($id);
        $doctypes = $em->getRepository('JordoUserBundle:SubscriptionDocType')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'year'        => (date('Y') + 1 + (date('m') > 8 ? -1 : 0)),
            'doctypes'    => $doctypes,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/roles", name="user_edit_roles")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_USER_ADMIN, ROLE_ADMIN")
     * @Template()
     */
    public function editRolesAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoUserBundle:User')->find($id);
        $groups = $em->getRepository('JordoUserBundle:Group')->findAll();
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return array(
            'entity' => $entity,
            'groups' => $groups,
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_USER_USER_ADMIN, ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="user_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_JORDO_USER_USER_ADMIN, ROLE_ADMIN")
     * @Template("JordoUserBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UserType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Secure(roles="ROLE_JORDO_USER_USER_ADMIN, ROLE_ADMIN")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Creates a form to delete a User entity by id.
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
