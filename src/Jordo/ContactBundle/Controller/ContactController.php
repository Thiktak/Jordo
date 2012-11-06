<?php

namespace Jordo\ContactBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\ContactBundle\Entity\Contact;
use Jordo\ContactBundle\Form\ContactType;

/**
 * Contact controller.
 *
 * @Route("/contact")
 */
class ContactController extends Controller
{
    static public function notifyWrite($sc, $action, $entity)
    {
        $who = sprintf(
            '<a href="%s">%s</a>',
            $entity->getUser() ? $sc->get('router')->generate('user_show', array('id' => $entity->getUser()->getId())) : '#',
            $entity->getUser() ?: 'Anonymous'
        );

        $contact = sprintf(
            '<a href="%s">%s</a>',
            $sc->get('router')->generate('contact_show', array('id' => $entity->getParam1()->getId())),
            $entity->getParam1()
        );
           
        // jordo.contact.event.$ACTION
        // create = %s a ajouté un évènement (%s)
        // update = %s a modifé une prise de contact avvec %s (par %s)
        switch( $action )
        {
            case 'update' : return sprintf('%s a modifié un contact (%s)', $who, $contact); break;
            case 'create' : return sprintf('%s a ajouté un contact (%s)', $who, $contact); break;
        }
    }

    static function applyFilters($t, $entities)
    {
        $userId = $t->get('security.context')->getToken()->getUser()->getId();
        $em = $t->getDoctrine()->getManager();
        
        $t->filter_contact = $t->get('session')->get('contact/contact', 'all');
        $t->filter_who     = $t->get('session')->get('contact/who',     'all');

        $t->filters = array();

        $t->filters['contact'] = $t->get('thiktak.core.orm')->createFilter($entities, $t->filter_contact);
        $t->filters['contact'] -> register('all',     function($entity) { return $entity; });
        $t->filters['contact'] -> register('never',   function($entity) {
            return $entity->leftjoin('c.infos', 'i1')
                          ->leftjoin('i1.calls', 'ca1')
                          ->GroupBy('c.id')
                          ->andHaving('COUNT(ca1.id) = 0');
        });
        $t->filters['contact'] -> register('again',   function($entity) {
            return $entity->leftjoin('c.infos', 'i1')
                          //->leftjoin('i1.calls', 'ca1')
                          ->groupBy('ca.id, c.id')
                          ->andWhere('ca.dateCallback >= CURRENT_TIMESTAMP()')
                          ->andHaving('COUNT(ca.id) > 0')
                          ;
        });


        $t->filters['who']   = $t->get('thiktak.core.orm')->createFilter($entities, $t->filter_who);
        $t->filters['who'] -> register('all', function($entity) { return $entity; });
        $t->filters['who'] -> register('me',  function($entity) use($userId) { return $entity->join('c.calls', 'ca2')->join('t.user', 'u')->where('u.id = :who')->setParameter('who', $userId); });
    }

    /**
     * Lists all Contact entities.
     *
     * @Route("/", name="contact")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contactRepository = $em->getRepository('JordoContactBundle:Contact');

        $contactRepository->findAll();
        $em->getRepository('JordoContactBundle:Info')->findAll();

        $entities = $contactRepository->createQueryBuilder('c')->select('c, i, ca, p')->orderBy('c.lname')->leftjoin('c.infos', 'i')->leftjoin('i.calls', 'ca')->leftjoin('c.projects', 'p');
        self::applyFilters($this, $entities);
        $entities = $entities->getQuery();
        //echo $entities->getSQL();
        $entities = $entities->getResult();


/*
        $aNom    = array('François', 'Dutronc', 'Dupont', 'Dupond', 'Morand', 'Capucin', 'Martel', 'MIDAS', 'MINERVE', 'QUASIMODO', 'TARTARIN', 'VENUS', 'ZEUS', 'BUFFON', 'CESAR', 'CHARLEMAGNE', 'CHARLES-MARTEL', 'CHOPIN', 'CLAUDEL', 'CLOVIS', 'CORNEILLE', 'DELACROIX', 'DESCARTES', 'DE LA FONTAINE', 'LANCELOT', 'LEPAPE', 'PERRAULT', 'PROUST', 'RACINE', 'SALOMON');
        $aPrenom = array('Marc', 'Xo', 'Paulinne', 'Céline', 'Pierre', 'Marie', 'Pierre-marie', 'André', 'Francis', 'Grégoire', 'Arthur', 'Melanie', 'Paul', 'Etienne', 'Francois', 'Mathilde', 'Eva', 'Jean-Marc', 'Herve', 'Claude', 'Capucine', 'Jean', 'Thomas', 'Thibaut', 'Xavier', 'Camille', 'Hugette', 'Davis', 'Alexis', 'Mohamed', 'Jules', 'Juliette', 'Nicolas', 'Paulette', 'Aurélie', 'Anais', 'Florent');

        $rand = function($list) { shuffle($list); return current($list); };

        for( $i = 0 ; $i < 500 ; $i++ )
        {
            $date = new \DateTime(rand(2000, 2012) . '-' . rand(1, 12) . '-' . rand(1, 31));
            echo "
INSERT INTO `contact` (`id`, `fname`, `lname`, `firm`, `addr`, `date_created`, `date_updated`, `type_id`) VALUES
(null, '", ucfirst(strtolower($rand($aPrenom))), "', '", ucfirst(strtolower($rand($aNom))), "', 'Société', '69 rue du Commerce', '" . $date->format('Y-m-d H:i:s') . "', '2007-01-01 00:00:00', " . rand(1, 4) . ");";
        }
//*/

        return array(
            'filters'  => $this->filters,
            'entities' => $entities,
        );
    }

    /**
     * Lists all User entities.
     *
     * @Route("/menu", name="contact_menu")
     * @Method("GET")
     * @Template()
     */
    public function menuAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contactRepository = $em->getRepository('JordoContactBundle:Contact');

        $entities = $contactRepository->createQueryBuilder('c')->select('c, i, ca, p')->orderBy('c.lname')->leftjoin('c.infos', 'i')->leftjoin('i.calls', 'ca')->leftjoin('c.projects', 'p');
        self::applyFilters($this, $entities);
        $entities = $entities->getQuery()->getResult();

        return array(
            'filters'  => $this->filters,
            'entities' => $entities,
        );
    }


    /**
     * @Route("/{type}:{filter}", name="contact_filter")
     */
    public function filterAction($type, $filter)
    {
        $this->get('session')->set('contact/' . $type, $filter);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * Creates a new Contact entity.
     *
     * @Route("/", name="contact_create")
     * @Method("POST")
     * @Template("JordoContactBundle:Contact:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Contact();
        $form = $this->createForm(new ContactType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('Thiktak.core.notify')->log($entity);

            return $this->redirect($this->generateUrl('contact_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Contact entity.
     *
     * @Route("/new", name="contact_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Contact();
        $form   = $this->createForm(new ContactType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Contact entity.
     *
     * @Route("/{id}", name="contact_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoContactBundle:Contact')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contact entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Contact entity.
     *
     * @Route("/{id}/edit", name="contact_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoContactBundle:Contact')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contact entity.');
        }

        $editForm = $this->createForm(new ContactType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Contact entity.
     *
     * @Route("/{id}", name="contact_update")
     * @Method("PUT")
     * @Template("JordoContactBundle:Contact:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoContactBundle:Contact')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contact entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ContactType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('Thiktak.core.notify')->log($entity);

            return $this->redirect($this->generateUrl('contact_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Contact entity.
     *
     * @Route("/{id}", name="contact_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoContactBundle:Contact')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Contact entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contact'));
    }

    /**
     * Creates a form to delete a Contact entity by id.
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
