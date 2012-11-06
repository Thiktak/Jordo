<?php

namespace Jordo\DocumentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jordo\DocumentBundle\Entity\Document;
use Jordo\DocumentBundle\Form\DocumentType;
use Doctrine\ORM\Query;

/**
 * Document controller.
 *
 * @Route("/document")
 */
class DocumentController extends Controller
{
    /**
     * @Route("/cron", name="document_cron")
     */
    public function cron()
    {
        set_time_limit(0);

        $em = $this->getDoctrine()->getManager();
        
        # On récupère les anciens fichiers
        $listOfAllDocs = array();
        $entities = $em->getRepository('JordoDocumentBundle:Document')->createQueryBuilder('d')
            ->orderBy('d.path', 'ASC')
            ->getQuery()->getResult();

        foreach( $entities as $doc )
            $listOfAllDocs[trim($doc->getPath(), '/') . '/' . trim($doc->getTitle(), '/')][$doc->getRevision()][$doc->getAction() ?: 'added'] = $doc;

        $oSVN = new SVNLastCommits('svn.iariss.fr', 'g.olivares', 'yh7gtzx');
        $SVN  = $oSVN -> open(4600, 'IARISS/trunk');

        echo '<pre>', print_r($SVN), '</pre>';

        foreach( $SVN as $document ) {
            foreach( array('added', 'deleted', 'modified', 'replaced') as $type )
            {
                if( isset($document->{$type . '-path'}) )
                    foreach( $document->{$type . '-path'} as $doc )
                    {
                        if( !isset($listOfAllDocs[ (string) $doc ][ (int) $document->{'version-name'} ][ $type ]) ) {
                            $o = new Document();
                            $o -> setSource('https://svn.iariss.fr/IARISS');
                        }
                        else
                            $o = $listOfAllDocs[ (string) $doc ][ (int) $document->{'version-name'} ][ $type ];

                        if( pathinfo((string) $doc, PATHINFO_EXTENSION) )
                        {
                            $o -> setPath(pathinfo((string) $doc, PATHINFO_DIRNAME));
                            $o -> setTitle(pathinfo((string) $doc, PATHINFO_BASENAME));
                        }
                        else
                            $o -> setPath((string) $doc);

                        $o -> setRevision((int) $document->{'version-name'});
                        $o -> setAction($type);
                        $o -> setDescription((string) $document->{'comment'});

                        $date = new \DateTime((string) $document->{'date'});
                        $o -> setDateAdded($date);

                        $oUser = $em->getRepository('JordoUserBundle:User')->findOneByUsername((string) $document->{'creator-displayname'});
                        if( $oUser )
                            $o->setUser($oUser);

                        $em->persist($o);

                        echo '<div>', strtoupper($type), ': ', (string) $doc, ' (', $date->format('Y-m-d H:i:s'), ')</div>';
                    }
            }
        }

        //$em->flush();
        exit();
    }


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
        $path = ($path ? '/' : null) . $path;

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
            ->setParameter('path', $path  . ($path ? '/' : null) . '%')
            ->orderBy('d.path', 'ASC')
            ->getQuery()
            ->getResult();

        foreach( $entities as $entity ) {
            $p = strstr(preg_replace('`^' . $path . '/?`', null, $entity->getPath() . '/'), '/', true);
            if( $p )
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
    public function indexAction()
    {
        $path = trim($this->getRequest()->get('path'), '/');
        $path = ($path ? '/' : null) . $path;

        $em = $this->getDoctrine()->getManager();

        $documentRepository = $entities = $em->getRepository('JordoDocumentBundle:Document');

        $entities = $documentRepository->createQueryBuilder('d')->select('d');
        //self::applyFilters($this, $entities);
        $entities = $entities
            ->select('d, c')
            ->leftjoin('d.comments', 'c')
            ->groupBy('d.path, d.title, d.revision')
            ->andWhere('d.path LIKE :path AND d.title IS NOT NULL')
            ->setParameter('path', $path . '%')
            ->addOrderBy('d.path', 'ASC')
            ->addOrderBy('d.title', 'ASC')
            ->addOrderBy('d.revision', 'ASC')
            ->getQuery()
            ->getResult();

        $entities2 = array();
        foreach( $entities as $entity ) {
            $i = $entity->getPath() . '/' . $entity->getTitle();
            if( !isset($entities2[$i]) OR $entities2[$i]->getRevision() < $entity->getRevision() ) {
                if( $entity->getAction() == 'deleted' )
                    unset($entities2[$i]);
                else
                    $entities2[$i] = $entity;
            }
        }

        return array(
            'filters'  => array(), //$this->filters,
            'entities' => $entities2,
            'path'     => $path,
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

            $this->get('Thiktak.core.notify')->log($entity);

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

        $revisions = $em->getRepository('JordoDocumentBundle:Document')->findBy(array(
            'path'  => $entity->getPath(),
            'title' => $entity->getTitle(),
        ), array('revision' => 'DESC'));

        $max = current($revisions);
        $max = $max->getId();

        if( $id != $max )
            return $this->redirect($this->generateUrl('document_show', array('id' => $max)));

        return array(
            'entity'    => $entity,
            'revisions' => $revisions,
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

        $this->get('Thiktak.core.notify')->log($entity);

        $editForm = $this->createForm(new DocumentType(), $entity);
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
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



Class SVNLastCommits
{
  protected $host;
  protected $username;
  protected $password;
  protected $port = 443;
  protected $path = '/';
  
  public function __construct($host, $username = null, $password = null)
  {
    $this -> host = $host;
    $this -> username = $username;
    $this -> password = $password;
  }
  
  public function setPath($path)
  {
    $this -> path = $path;
  }
  
  public function open($start_revision, $path = null)
  {
    // http://svn.apache.org/repos/asf/subversion/trunk/notes/http-and-webdav/webdav-protocol
    $request =
    '<?xml version="1.0"?>'.
    '<S:log-report xmlns:S="svn:">'.
      '<S:start-revision>'.$start_revision.'</S:start-revision>'.
      '<S:discover-changed-paths/>'.
    '</S:log-report>';

    $header =
    "REPORT /".trim($path ?: ($this->path), ' /')." HTTP/1.1\r\n".
    "Host: ".$this->host."\r\n".
    "Depth: 1\r\n".
    "User-Agent: PHP-Code\r\n".
    "Content-type: text/xml\r\n".
    "Content-length: ".strlen($request)."\r\n".
    "Authorization: Basic ".base64_encode($this->username . ':' . $this->password)."\r\n".
    "\r\n";

    $sock = fsockopen("ssl://".$this->host, $this->port);
    if(!$sock) return false;
    if(!fputs($sock, $header.$request)) return false;

    $str = stream_get_contents($sock);
    $code = substr($str, strpos($str, ' ')+1, 3);
    if($code != 200) return false;

    $str = substr($str, strpos($str, "<"));
    $str = substr($str, 0, strrpos($str, ">")+1);
    $str = str_replace(array('<S:', '</S:', '<D:', '</D:'), array('<', '</', '<', '</'), $str);
    return simplexml_load_string($str);
  }
}