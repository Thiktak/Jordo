<?php

namespace Jordo\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Jordo\ProjectBundle\Entity\Team;
use Jordo\ProjectBundle\Form\TeamType;

/**
 * Team controller.
 *
 */
class TeamController extends Controller
{
    /**
     * Lists all Team entities.
     *
     * @Route("project-{project}/team/", name="project_team")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JordoProjectBundle:Team')->findAll();

        return array(
            'entities' => $entities,
        );
    }


    /**
     * Lists all Team entities.
     *
     * @Route("project-{project}/team/widget", name="project_team_widget")
     * @Method("GET")
     * @Template("JordoProjectBundle:Team:widget.html.twig")
     */
    public function widgetAction($project)
    {
        $em = $this->getDoctrine()->getManager();

        $oProject = $em->getRepository('JordoProjectBundle:Project')->find($project);
        $oEntities = $em->getRepository('JordoProjectBundle:Team')->findBy(array('project' => $project));

        return array(
            'entities' => $oEntities,
            'project'  => $oProject,
        );
    }

    /**
     * Creates a new Team entity.
     *
     * @Route("project-{project}/team/create", name="project_team_create")
     * @Method("POST")
     * @Template("JordoProjectBundle:Team:new.html.twig")
     */
    public function createAction(Request $request, $project)
    {
        $em = $this->getDoctrine()->getManager();
        $oProject = $em->getRepository('JordoProjectBundle:Project')->find($project);

        $entity  = new Team();
        $entity->setProject($oProject);

        $form = $this->createForm(new TeamType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('Thiktak.core.notify')->log($entity);

            return $this->redirect($this->generateUrl('project_team_show', array('project' => $project, 'id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Team entity.
     *
     * @Route("project-{project}/team/new", name="project_team_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Team();
        $form   = $this->createForm(new TeamType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Team entity.
     *
     * @Route("project-{project}/team-{id}", name="project_team_show", defaults={"project"=0})
     * @Method("GET")
     * @Template()
     */
    public function showAction($project = null, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Team')->find($id);
        $oProject = $em->getRepository('JordoProjectBundle:Project')->find($project ?: $entity->getProject()->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Team entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'project'     => $oProject,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Team entity.
     *
     * @Route("project-{project}/team-{id}/edit", name="project_team_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Team')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Team entity.');
        }

        $editForm = $this->createForm(new TeamType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Team entity.
     *
     * @Route("project/{project}/team-{id}", name="project_team_update")
     * @Method("PUT")
     * @Template("JordoProjectBundle:Team:edit.html.twig")
     */
    public function updateAction(Request $request, $project, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JordoProjectBundle:Team')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Team entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TeamType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('project_team_edit', array('project' => $project, 'id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Team entity.
     *
     * @Route("project/{project}/team-{id}", name="project_team_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $project, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JordoProjectBundle:Team')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Team entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project_team', array('project' => $project)));
    }

    /**
     * Creates a form to delete a Team entity by id.
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
