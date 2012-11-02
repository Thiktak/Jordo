<?php

namespace Thiktak\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Finder\Finder;

class SearchController extends Controller
{
/**
     * @Route("/search/{search}", defaults={"search" = null}, name = "search")
     * @Template()
     */
    public function indexAction($search = null)
    {
        $post_search = $this->getRequest()->query->get('search');
        if( $post_search )
            return $this->redirect(
                $this->generateUrl('search', array('search' => $post_search))
            );

        $em = $this->getDoctrine()->getEntityManager();
        
        # Stockage
        $results    = array();
        $resultsAll = array();

        # On sépare chaque terme (gestion des ".*" et .*)
        $i = 0;
        $words = array();
        if( preg_match_all('`("(.[^"]*)"|(.[^ ]*))`sUi', $search, $m) )
            foreach( $m[1] as $r ) {
                if( $r == ' ' )
                    $i++;
                else
                    $words[$i] = (isset($words[$i]) ? $words[$i] : null) . $r;
            }

        # On nettoie le tout
        foreach( $words as $i => $j )
            $words[$i] = trim($j, ' "');

        # On parcours toutes les classes
        $kernel = $this->get('kernel');
        $finder = new Finder();

        foreach( $kernel -> getBundles() as $bundle )
        {
            if( file_exists($bundle->getPath() . DIRECTORY_SEPARATOR . 'Controller') )
                foreach( $finder->files()->name('*Controller.php')->in($bundle->getPath() . DIRECTORY_SEPARATOR . 'Controller') as $file )
                {
                    $className = $bundle->getNameSpace() . '\\Controller\\' . basename($file, '.php');
                    if( !class_exists($className) || !is_subclass_of($className, 'Thiktak\SearchBundle\Base\BaseSearch') )
                        continue;
            
                    $oM = new \ReflectionMethod($className, 'execute');
                    
                    $return = $oM->invoke(new $className);
                    $return = array_merge(array('class' => null, 'icon' => null, 'name' => str_ireplace('controller', null, basename($file, '.php')), 'entity' => null, 'query' => null, 'prepare' => null, 'render' => null), $return);

                    if( !isset($return['entity']) )
                        continue;

                    //$return = call_user_func(array($className, 'execute'));
                    
                    $sql = $em->getRepository($return['entity'])->createQueryBuilder('x');//->select('DISTINCT x');

                    if( $return['prepare'] )
                        $sql = $return['prepare']($sql);

                    foreach( $words as $i => $word )
                        $sql = $return['search']($sql, $word, 'word' . $i);

                    $sql = $sql->setMaxResults(50)->getQuery()->getArrayResult();

                    $results[$className]['datas'] = array();
                    foreach( $sql as $row )
                        $results[$className]['datas'][] = $row;

                    $results[$className]['name']   = $return['name'];
                    $results[$className]['render'] = $return['render'];
                    $results[$className]['class']  = $return['class'];
                    $results[$className]['icon']   = $return['icon'];

                    // @TODO: method_exists(@, 'execute')
                    // @TODO: $em
                    //$results[$class] = call_user_func(array($class, 'execute'), $em, $words);
                    //$types[$class] = str_ireplace('bundle', '', current(array_slice(array_reverse(explode('\\', trim(str_ireplace('SearchController', '', $class), '\\'))), 1)));
                }
        }

        $i = 0;
        foreach( $results as $type => $datas )
            foreach( $datas['datas'] as $data ) {
                $data2 = $data;

                foreach( $data2 as $key => $val )
                    if( !is_string($val) )
                        unset($data2[$key]);

                $l = levenshtein($search, implode(' ', $data2));
                $resultsAll[$l . '.' . $i++] = array(
                    'class'  => $datas['class'],
                    'icon'   => $datas['icon'],
                    'render' => $this->render($datas['render'], array('entity' => $data))->getContent()
                );
            }
        ksort($resultsAll);

        return array('search' => $search, 'results' => $results, 'resultsAll' => $resultsAll);
    }
}
