<?php

namespace Jordo\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\ProjectBundle\Entity\Project;
use Jordo\ProjectBundle\Form\ProjectType;

/**
 * Project controller.
 *
 * @Route("/project")
 */
class ProjectController extends Controller
{
    static function applyFilters($t, $entities)
    {
        $userId = $t->get('security.context')->getToken()->getUser()->getId();
        $em = $t->getDoctrine()->getManager();
        
        $t->current = $current = date('Y') + 1 + (date('m') > 8 ? -1 : 0);
        $t->filter_state = $t->get('session')->get('project/state', 'all');
        $t->filter_who   = $t->get('session')->get('project/who',   'all');

        $t->filters = array();

        $t->filters['state'] = $t->get('thiktak.core.orm')->createFilter($entities, $t->filter_state);
        $t->filters['state'] -> register('all',     function($entity) { return $entity; });
        foreach( array('draft', 'devis', 'progress', 'wait', 'cancel', 'close') as $state )
            $t->filters['state'] -> register($state, function($entity) use($state) { return $entity->andWhere('p.state = :state')->setParameter('state', $state); });


        $t->filters['who']   = $t->get('thiktak.core.orm')->createFilter($entities, $t->filter_who);
        $t->filters['who'] -> register('all', function($entity) { return $entity; });
        $t->filters['who'] -> register('me',  function($entity) use($userId) { return $entity->join('p.team', 't')->join('t.user', 'u')->where('u.id = :who')->setParameter('who', $userId); });
    }
    

    /**
     * @Route("/{type}:{filter}", name="project_filter")
     */
    public function filterAction($type, $filter)
    {
        $this->get('session')->set('project/' . $type, $filter);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    

    /**
     * @Route("/print/{what}-{id}", name="project_print")
     * @Template()
     */
    public function printAction($what, $id)
    {
        return $this->{$what . 'Action'}($id);
    }


    /**
     * Lists all Project entities.
     *
     * @Route("/", name="project")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_PROJECT_PROJECT_LIST, ROLE_ADMIN")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $userRepository = $em->getRepository('JordoProjectBundle:Project');

        $entities = $userRepository->createQueryBuilder('p')->select('p');
        self::applyFilters($this, $entities);
        $entities = $entities->getQuery()->getResult();

        return array(
            'filters'  => $this->filters,
            'entities' => $entities,
        );
    }

    /**
     * Lists all Project entities.
     *
     * @Route("/menu", name="project_menu")
     * @Method("GET")
     * @Template()
     */
    public function menuAction()
    {
        $em = $this->getDoctrine()->getManager();

        $userRepository = $entities = $em->getRepository('JordoProjectBundle:Project');

        $entities = $userRepository->createQueryBuilder('p')->select('p');
        self::applyFilters($this, $entities);
        $entities = $entities->getQuery()->getResult();

        return array(
            'filters'  => $this->filters,
            'entities' => $entities,
        );
    }

    /**
     * Lists all Project entities.
     *
     * @Route("/widget/{type}-{id}", name="call_widget")
     * @Method("GET")
     * @Template()
     */
    public function widgetAction($type, $id)
    {
        $em = $this->getDoctrine()->getManager();

        switch( $type )
        {
            case 'user':
                $entities = $em->getRepository('JordoProjectBundle:Project')->createQueryBuilder('p')
                               ->join('p.team', 't')
                               ->join('t.user', 'u')
                               ->where('u.id = :id')
                               ->setParameter('id', $id)
                               ->getQuery()
                               ->getResult();
                break;

            case 'contact':
                $entities = $em->getRepository('JordoProjectBundle:Project')->findByContact($id);
                break;

            default: die(); break;
        }

        return array(
            'entities' => $entities,
            'type'     => $type,
            'value'    => $id,
        );
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/", name="project_create")
     * @Method("POST")
     * @Secure(roles="ROLE_JORDO_PROJECT_PROJECT_ADMIN, ROLE_ADMIN")
     * @Template("JordoProjectBundle:Project:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Project();
        $form = $this->createForm(new ProjectType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('project_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="project_new")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_PROJECT_PROJECT_ADMIN, ROLE_ADMIN")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Project();
        $form   = $this->createForm(new ProjectType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}.{_format}", name="project_show", defaults={"_format"="html"})
     * @Secure(roles="ROLE_JORDO_PROJECT_PROJECT_LIST, ROLE_ADMIN")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity   = $em->getRepository('JordoProjectBundle:Project')->find($id);
        $doctypes = $em->getRepository('JordoProjectBundle:DocumentType')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        
        $types2 = array();
        foreach( $entity->getDocuments() as $en )
            $types2[$en->getDoctype()->getId()]['children'][] = $en;

        foreach( $doctypes as $i => $type ) {
            $i = $type->getId();
            $types2[$i]['type']     = $type;
            if( !isset($types2[$i]['children']) )
                $types2[$i]['children'] = array();

            $nb = $type->getIsNumberofIntervenants() ? count($entity->getTeam()) : 1;
            for( $j = count($types2[$i]['children']) ; $j < $nb ; $j++ ) // getTeam(true) : isIntervenant
                $types2[$i]['children'][] = null;
        }

        $nbdoctypes = 0;
        foreach( $doctypes as $doc )
            $nbdoctypes += $doc->getIsNumberofIntervenants() ? count($entity->getTeam()): 1;

        return array(
            'doctypes'    => $doctypes,
            'nbdoctypes'  => $nbdoctypes,
            'types'       => $types2,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/{id}/edit", name="project_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_JORDO_PROJECT_PROJECT_ADMIN, ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $editForm = $this->createForm(new ProjectType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/{id}", name="project_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_JORDO_PROJECT_PROJECT_ADMIN, ROLE_ADMIN")
     * @Template("JordoProjectBundle:Project:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProjectType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('project_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Project entity.
     *
     * @Route("/{id}", name="project_delete")
     * @Method("DELETE")
     * @Secure(roles="ROLE_JORDO_PROJECT_PROJECT_ADMIN, ROLE_ADMIN")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoProjectBundle:Project')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Project entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project'));
    }

    /**
     * Creates a form to delete a Project entity by id.
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
