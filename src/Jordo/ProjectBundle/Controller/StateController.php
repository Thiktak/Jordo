<?php

namespace Jordo\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\ProjectBundle\Entity\State;

/**
 * Document controller.
 *
 * @Route("/project/state")
 */
class StateController extends Controller
{
    /**
     * Edits an existing Document entity.
     *
     * @Route("/project-{project}/state-{state}", name="project_state_set")
     * @Method("GET")
     * @Template("JordoProjectBundle:Document:edit.html.twig")
     */
    public function setAction(Request $request, $project, $state)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Project')->find($project);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        if (!in_array($state, array('draft', 'devis', 'progress', 'wait', 'cancel', 'close'))) {
            throw $this->createNotFoundException('Unable to find State entity.');
        }

        $oState = new State();
        $oState -> setTitle($state);
        $oState -> setProject($entity);
        $oState -> setUser($this->get('security.context')->getToken()->getUser());
        $entity->addState($oState);
        $entity->setState($state);

        $em->persist($oState);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
