<?php

namespace Jordo\StatsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Finder\Finder;

class IndexController extends Controller
{

    /**
     * @Route("/stats", name="stats")
     * @Template()
     */
    public function indexAction()
    {
        $return = array('X' => array(), 'Y' => array());
        $get['X'] = $this->getRequest()->get('x');
        $get['Y'] = $this->getRequest()->get('y');
        $format = $this->getRequest()->get('format', 'Ymd');

        $options['stack']   = true;
        $options['xaxis']   = 'date';
        $options['type']    = 'bar'; // donut
        $options['reverse'] = false;
        $options['format']  = $format;
        $options['cstack']  = false;
        //$options = new \ArrayObject($options);
        // char, line, pie

        self::applyFilters($this, $options);

        switch( $options['format'] )
        {
            case 'Ymd' : $format = '%Y-%m-%d'; break;
            case 'Ym' :  $format = '%Y-%m'; break;
            case 'Y' :   $format = '%Y'; break;
            case 'w' :   $format = '%w - %W'; break;
            case 't' :   $format = '%m - %M'; break;
        }

        $axis = $this->getAxis();


        $em = $this->getDoctrine()->getManager();

        foreach( array('X', 'Y') as $_type )
        {
            if( $get[$_type] ) {
                if( isset($axis[$_type][$get[$_type]]['id']) ) {

                    // ON récupère la classe
                    $content = call_user_func_array(explode('::', $axis[$_type][$get[$_type]]['id']), array());
                    $content = array_merge(array(
                      'entity' => null, 'select' => null, 'val' => null, 'key' => null, 'groupBy' => null, 'join' => null, 'where' => null,
                    ), (array) $content);


                    // On prépare la requête (ordre)
                    $sql = array();

                    if( preg_match('`date`', $content['key']) )
                        $content['key'] = 'DATE_FORMAT(' . $content['key'] . ', \'' . $format . '\')';

                    $content['val'] = (array) $content['val'];

                    foreach( $content['val'] as $k => $v )
                        $content['val'][$k] = $v . ' as val' . $k;

                    $group = array('key');
                    for( $i = 0 ; $i < count($content['val'])-1 ; $i++ )
                      $group[] = 'val' . $i;

                    // On construit la requête
                    $sql[0] = 'SELECT DISTINCT ' . implode(', ', $content['val']) . ', ' . $content['key'] . ' as key';
                    $sql[1] = 'FROM ' . $content['entity'] . ' x';
                    if( $content['join']  ) $sql[2] = 'JOIN ' . implode(' JOIN ', explode(',', $content['join']));
                    if( $content['where'] ) $sql[3] = 'WHERE ' . $content['where'];
                    $sql[5] = 'ORDER BY key';
                    $sql[5] = 'GROUP BY ' . implode(',', $group);
                    
                    $sql = implode(' ', $sql);

                    $return[$_type] = $this->getDoctrine()->getManager()->createQuery($sql)->getArrayResult();
                    if( $options['reverse'] )
                        foreach( $return[$_type] as $a => $item ) {
                            $c = $item['key'];
                            $return[$_type][$a]['key']  = $item['val0'];
                            $return[$_type][$a]['val0'] = $c;
                        }
                }
            }
        }

        $keys = array();

        if( !isset($return['Y']) )
            foreach( $return['X'] as $x ) {
                $return['Y'][$x['key'] ?: 0] = array('val' => $x['key'] ?: 0, 'key' => $x['key'] ?: 0);
                $keys = array_merge($keys, array($x['key'] ?: 0));
            }


        $returnXY = array();
        $values   = array();

        foreach( $return['X'] as $X )
          if( count($X) > 2 )
          {
            for( $i = 1 ; $i < count($X) - 1 ; $i++ ) {
              $returnXY[$X['val0'] ?: 0][$X['key'] ?: 0] = $X['val' . $i] ?: 0;
              
              foreach( $keys as $k )
                if( !isset($returnXY[$X['val0'] ?: 0][$k ?: 0]) )
                  $returnXY[$X['val0'] ?: 0][$k ?: 0] = 0;
              
              ksort($returnXY[$X['val0'] ?: 0]);

              $values[] = $X['val' . $i];

              /*
              $t = 0;
              foreach( $returnXY[$X['val0'] ?: 0] as $a => $b )
                  $t = $returnXY[$X['val0'] ?: 0][$a] = $t + $b;
              // */
            }
          }
          else
          {
            $returnXY[0][$X['key'] ?: 0] = $X['val0'] ?: 0;
            
            foreach( $keys as $k )
              if( !isset($returnXY[$X['val0'] ?: 0][$X['key'] ?: 0]) )
                $returnXY[$X['val0'] ?: 0][$k ?: 0] = 0;
            
            if( isset($returnXY[$X['val0'] ?: 0]) )
                ksort($returnXY[$X['val0'] ?: 0]);

            $values[] = $X['val0'] ?: 0;
          }

          if( $options['cstack'] )
              foreach( $returnXY as $i => $datas1 ) {
                  $t = 0;
                  foreach( $datas1 as $a => $b )
                      $t = $returnXY[$i][$a] = $b + $t;

                  $values[] = $t;
              }

          if( $options['cstack'] && $options['stack'] )
              foreach( $returnXY as $i => $datas1 )
                $values[] = array_sum($datas1);


        ob_start(); var_dump($returnXY); $dump = ob_get_clean();


        var_dump($options);
        return array(
            'max'     => $values ? max($values) : null,
            'min'     => $values ? min(0, min($values)) : null,
            'filters' => $this->filters,
            'datas'   => $returnXY,
            'options' => $options,
            'dump'    => $dump,
        );
    }

    /**
     * @Route("/stats/menu", name="stats_menu")
     * @Template()
     */
    public function menuAction()
    {
        $axis = $this->getAxis();
        $entities = array();

        return compact('axis', 'entities');
    }


    /**
     * @Route("/stats/{type}:{filter}", name="stats_filter")
     */
    public function filterAction($type, $filter)
    {
        $this->get('session')->set('stats/' . $type, $filter);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }


    static function applyFilters($t, &$options)
    {
        $t->filter_stack   = $t->get('session')->get('stats/stack',   'no');
        $t->filter_type    = $t->get('session')->get('stats/type',    'line');
        $t->filter_format  = $t->get('session')->get('stats/format',  'Ymd');
        $t->filter_xaxis   = $t->get('session')->get('stats/xaxis',   'line');
        $t->filter_reverse = $t->get('session')->get('stats/reverse', 'no');
        $t->filter_cstack  = $t->get('session')->get('stats/cstack',  'no');

        $t->filters = array();
        $t->filters['stack'] = $t->get('thiktak.core.orm')->createFilter($options, $t->filter_stack);
        $t->filters['stack'] -> register('yes', function($entity) { $entity['stack'] = true;  return $entity; });
        $t->filters['stack'] -> register('no',  function($entity) { $entity['stack'] = false; return $entity; });
        $options = $t->filters['stack']->getEntity();

        $t->filters['cstack'] = $t->get('thiktak.core.orm')->createFilter($options, $t->filter_cstack);
        $t->filters['cstack'] -> register('yes', function($entity) { $entity['cstack'] = true;  return $entity; });
        $t->filters['cstack'] -> register('no',  function($entity) { $entity['cstack'] = false; return $entity; });
        $options = $t->filters['cstack']->getEntity();

        $t->filters['type'] = $t->get('thiktak.core.orm')->createFilter($options, $t->filter_type);
        $t->filters['type'] -> register('line',  function($entity) { $entity['type'] = 'line';  return $entity; });
        $t->filters['type'] -> register('bar',   function($entity) { $entity['type'] = 'bar';   return $entity; });
        $t->filters['type'] -> register('donut', function($entity) { $entity['type'] = 'donut'; return $entity; });
        $options = $t->filters['type']->getEntity();

        $t->filters['format'] = $t->get('thiktak.core.orm')->createFilter($options, $t->filter_format);
        $t->filters['format'] -> register('Y',     function($entity) { $entity['format'] = 'Y';   return $entity; });
        $t->filters['format'] -> register('Y-m',   function($entity) { $entity['format'] = 'Ym';  return $entity; });
        $t->filters['format'] -> register('Y-m-d', function($entity) { $entity['format'] = 'Ymd'; return $entity; });
        $t->filters['format'] -> register('w',     function($entity) { $entity['format'] = 'w';   return $entity; });
        $t->filters['format'] -> register('t',     function($entity) { $entity['format'] = 't';   return $entity; });
        $options = $t->filters['format']->getEntity();

        $t->filters['reverse'] = $t->get('thiktak.core.orm')->createFilter($options, $t->filter_reverse);
        $t->filters['reverse'] -> register('yes', function(&$entity) { $entity['reverse'] = true; return $entity; });
        $t->filters['reverse'] -> register('no',  function(&$entity) { $entity['reverse'] = false; return $entity; });
        $options = $t->filters['reverse']->getEntity();

        $t->filters['xaxis'] = $t->get('thiktak.core.orm')->createFilter($options, $t->filter_xaxis);
        $t->filters['xaxis'] -> register('date', function($entity) { $entity['xaxis'] = 'date'; return $entity; });
        $t->filters['xaxis'] -> register('char', function($entity) { $entity['xaxis'] = 'char'; return $entity; });
        $options = $t->filters['xaxis']->getEntity();

        return $options;
    }


    protected function getAxis()
    {
        $cacheFile = dirname(__FILE__) . '/../Resources/config/axes.php.cache';

        $axis = array();
        $axis['X'] = array();
        $axis['Y'] = array();


        if( !file_exists($cacheFile) OR $this->getRequest()->get('maj') ) {
            $kernel = $this->get('kernel');

            $finder = new Finder();

            foreach( $kernel -> getBundles() as $bundle )
            {
                if( file_exists($bundle->getPath() . DIRECTORY_SEPARATOR . 'Controller') )
                    foreach( $finder->files()->name('*Controller.php')->in($bundle->getPath() . DIRECTORY_SEPARATOR . 'Controller') as $file )
                    {
                        $className = $bundle->getNameSpace() . '\\Controller\\' . basename($file, '.php');
                        if( !class_exists($className) || !is_subclass_of($className, 'Jordo\StatsBundle\Base\BaseStatistique') )
                            continue;

                        $oClass = new \ReflectionClass($className);
                        foreach( $oClass -> getMethods() as $method )
                            if( preg_match('`^defineAxis(.*)`', $method->getName(), $m) )
                            {
                                $id = $oClass->getName() . '::' . $m[0];

                                $axis['X'][$id] = array(
                                  'text'   => $m[1],
                                  'id'     => $id,
                                  'class'  => array($oClass->getName(), $m[0]),
                                  //'method' => $method,
                                );
                            }
                    }

            }

            file_put_contents($cacheFile, '<?php $axis = ' . var_export($axis, true) . '; ?>');
        }
        else
          include $cacheFile;

/*
        $o = new \ReflectionClass(__NAMESPACE__ . '\Stat1');
        foreach( $o -> getMethods() as $method )
          if( preg_match('`^defineAxis(.*)`', $method->getName(), $m) )
          {
            $id = __NAMESPACE__ . '\Stat1::' . $m[0];

            $axis['X'][$id] = array(
              'text'   => $m[1],
              'id'     => $id,
              'class'  => array(__NAMESPACE__ . '\Stat1', $m[0]),
              'method' => $method,
            );
          }

        $axis['Y'] = $axis['X'];
*/
        return $axis;
    }
}


//Class BaseStatistique { }

/*
 * @param groupBy y|m|d|w|...
 */
/*
Class Stat1 extends BaseStatistique
{
    static public function defineAxisA()
    {
        // SELECT COUNT(x.id) as val, DATE_FORMAT(x.dateStart,"%Y-%m") as key FROM Jordo\ContactBundle\Entity\Call x

        // $sql = 'SELECT COUNT(x.id) as val, DATE_FORMAT(x.dateStart,\'%Y-%m\') as key FROM Jordo\ContactBundle\Entity\Call x GROUP BY key';

        return array(
          'entity' => 'Jordo\ContactBundle\Entity\Call',
          'val' => 'COUNT(x.id)',
          'key' => 'x.dateStart',
        );
    }

    static public function defineAxisB()
    {
        return array(
          'entity' => 'Jordo\ContactBundle\Entity\Type',
          'join'   => 'x.contact as y, y.infos as i, i.calls as c',
          'val'    => array('x.title', 'COUNT(c.id)'),
          'key'    => 'c.dateStart',
        );
    }
}
//*/