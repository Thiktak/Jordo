<?php

namespace Jordo\DocumentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jordo\DocumentBundle\Entity\Document;
use Jordo\DocumentBundle\Form\DocumentType;

/**
 * Document controller.
 *
 * @Route("/document")
 */
class DocumentController extends Controller
{
    static function applyFilters($t, $entities)
    {
        $userId = $t->get('security.context')->getToken()->getUser()->getId();
        $em = $t->getDoctrine()->getManager();
        
        $t->current = $current = date('Y') + 1 + (date('m') > 8 ? -1 : 0);
        $t->filter_state = $t->get('session')->get('document/state', 'last');
        $t->filter_who   = $t->get('session')->get('document/who',   'all');

        $t->filters = array();

        $t->filters['state'] = $t->get('thiktak.core.orm')->createFilter($entities, $t->filter_state);

        $t->filters['state'] -> register('dir',  function($entity) { return $entity->orderBy('d.path', 'ASC'); });
        $t->filters['state'] -> register('last', function($entity) { return $entity->orderBy('d.dateCreated', 'DESC'); });
        $t->filters['state'] -> register('read', function($entity) { return $entity; });


        $t->filters['who']   = $t->get('thiktak.core.orm')->createFilter($entities, $t->filter_who);
        
        $t->filters['who'] -> register('all', function($entity) { return $entity; });
        $t->filters['who'] -> register('me',  function($entity) use($userId) { return $entity->join('d.user', 'u')->where('u.id = :who')->setParameter('who', $userId); });
    }

    /**
     * @Route("/{type}:{filter}", name="document_filter")
     */
    public function filterAction($type, $filter)
    {
        $this->get('session')->set('document/' . $type, $filter);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Template
     */
    public function menuAction()
    {
        $path = trim($this->getRequest()->get('path'), '/');

        $paths = array(); // ? array( realpath($path . '/../') => '@' . trim($path, '/') ) : array();

        if( $path )
        {
            $up = explode('/', $path);
            $paths[ implode(array_slice($up, 0, count($up) - 1), '/') ] = '..';
        }

        $em = $this->getDoctrine()->getManager();
        $entities = $em
            ->getRepository('JordoDocumentBundle:Document')
            ->createQueryBuilder('d')
            ->where('d.path LIKE :path')
            ->setParameter('path', '' . $path  . ($path ? '/' : null) . '%')
            ->orderBy('d.path', 'ASC')
            ->getQuery()
            ->getResult();

        foreach( $entities as $entity ) {
            $p = strstr(preg_replace('`^' . $path . '/?`', null, $entity->getPath()), '/', true);
            $paths[trim($path . '/' . $p, '/')] = $p;
        }

        return compact('paths');
    }
    

    /**
     * Lists all Document entities.
     *
     * @Route("/", name="document")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($sort = 'dir')
    {
        $path = trim($this->getRequest()->get('path'), '/');

        if( !in_array($sort, array('dir', 'last', 'read')) )
            throw new \Exception('Système de tri innexistant.');

        $em = $this->getDoctrine()->getManager();

        $documentRepository = $entities = $em->getRepository('JordoDocumentBundle:Document');

        $entities = $documentRepository->createQueryBuilder('d')->select('d');
        self::applyFilters($this, $entities);
        $entities = $entities
            ->andWhere('d.path LIKE :path')
            ->setParameter('path', $path  . ($path ? '/' : null) . '%')
            ->getQuery()
            ->getResult();

        return array(
            'filters'  => $this->filters,
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Document entity.
     *
     * @Route("/", name="document_create")
     * @Method("POST")
     * @Template("JordoDocumentBundle:Document:new.html.twig")
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

            return $this->redirect($this->generateUrl('document_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Document entity.
     *
     * @Route("/new", name="document_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Document();
        $form   = $this->createForm(new DocumentType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Document entity.
     *
     * @Route("/{id}", name="document_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoDocumentBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Document entity.
     *
     * @Route("/{id}/edit", name="document_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoDocumentBundle:Document')->find($id);

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
     * @Route("/{id}", name="document_update")
     * @Method("PUT")
     * @Template("JordoDocumentBundle:Document:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoDocumentBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DocumentType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('document_show', array('id' => $id)));
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
     * @Route("/{id}", name="document_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoDocumentBundle:Document')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Document entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('document'));
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
