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
use Symfony\Component\Finder\Finder;

/**
 * Group controller.
 *
 * @Route("/group/roles")
 */
class RoleController extends Controller
{

    public function getAllRoles()
    {
        $roles = array();
        
        $roles[] = 'ROLE_USER';
        $roles[] = 'ROLE_MEMBER';
        $roles[] = 'ROLE_ANCIEN';
        $roles[] = 'ROLE_ADMIN';
        /*
        $roles[] = 'ROLE_GROUP_QUALITY';
        $roles[] = 'ROLE_GROUP_PRESIDENCE';

        $roles[] = 'ROLE_JORDO_USER_SUBSCRIPTION_VIEW';
        $roles[] = 'ROLE_JORDO_USER_SUBSCRIPTION_ADMIN';

        $roles[] = 'ROLE_JORDO_DOCUMENT_VIEW';
        $roles[] = 'ROLE_JORDO_DOCUMENT_ADMIN';*/

        $kernel = $this->get('kernel');

        $finder = new Finder();

        foreach( $kernel -> getBundles() as $bundle )
        {
            if( file_exists($bundle->getPath() . DIRECTORY_SEPARATOR . 'Controller') )
                foreach( $finder->files()->name('*Controller.php')->in($bundle->getPath() . DIRECTORY_SEPARATOR . 'Controller') as $file )
                {
                    $className = $bundle->getNameSpace() . '\\Controller\\' . basename($file, '.php');
                    if( !class_exists($className) || !is_subclass_of($className, 'Symfony\Bundle\FrameworkBundle\Controller\Controller') )
                        continue;

                    $oClass = new \ReflectionClass($className);
                    foreach( $oClass->getMethods() as $method ) {
                        if( preg_match('`@Secure\(roles=(.[^\)]*)\)`si', $method->getDocComment(), $m) )
                            foreach( explode(',', trim(str_replace(' ', '', $m[1]), ' "()')) as $role )
                                $roles[$role] = $role;
                    }
                }

        }

        sort($roles);
        return $roles;
    }

    /**
     * Lists all Group/user entities.
     *
     * @Route("/user/{id}",  name="group_role_user",  defaults={"type"="user"})
     * @Route("/group/{id}", name="group_role_group", defaults={"type"="group"})
     * @Method("GET")
     * @Template()
     */
    public function widgetAction($type, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $roles = $this->getAllRoles();
        $entity = array();
    
        $entities = array();
        if( count($roles) )
            $entities = array_combine($roles, array_fill(0, count($roles), null));

        switch($type)
        {
            case 'group' :
                $entity = $em->getRepository('JordoUserBundle:Group')->find($id);
                foreach( $entity->getRoles() as $role )
                    if( array_key_exists($role, $entities) )
                        $entities[$role] = true;
                break;

            case 'user' :
                $entity = $em->getRepository('JordoUserBundle:User')->find($id);
                foreach( $entity->getRoles() as $role )
                    if( array_key_exists($role, $entities) )
                        $entities[$role] = true;
                break;
        }

        return array(
            'id'       => $id,
            'type'     => $type,
            'entity'   => $entity,
            'entities' => $entities,
        );
    }

    /**
     * update Group/user entities.
     *
     * @Route("/update/{type}/{id}",  name="group_role_update")
     * @Method("POST")
     * @Template("JordoUserBundle:Role:widget.html.twig")
     */
    public function updateAction(Request $request, $type, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $roles = $this->getAllRoles();
        $entity = array();
        $entities = array_combine($roles, array_fill(0, count($roles), null));

        switch($type)
        {
            case 'group' :
                $entity = $em->getRepository('JordoUserBundle:Group')->find($id);
                
                $newRoles = array();
                foreach( (array) $request->get('role') as $roleName => $role )
                    if( $role )
                        $newRoles[] = $roleName;

                $entity->setRoles($newRoles);
                $em->persist($entity);
                $em->flush();

                return $this->redirect($request->headers->get('referer'));

                break;

            case 'user' :
                $entity = $em->getRepository('JordoUserBundle:User')->find($id);
                
                $newRoles = array();
                foreach( (array) $request->get('role') as $roleName => $role )
                    if( $role )
                        $newRoles[] = $roleName;

                $entity->setRoles($newRoles);
                $em->persist($entity);
                $em->flush();

                return $this->redirect($request->headers->get('referer'));
                break;
        }

        return array(
            'id'       => $id,
            'type'     => $type,
            'entity'   => $entity,
            'entities' => $entities,
        );
    }
}
