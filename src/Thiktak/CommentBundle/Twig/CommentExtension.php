<?php

namespace Thiktak\CommentBundle\Twig;

use Doctrine\ORM\EntityManager;

class CommentExtension extends \Twig_Extension
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array(
            'getNumberOfComments' => new \Twig_Function_Method($this, 'getNumberOfComments'),
            'getStateOfComments'  => new \Twig_Function_Method($this, 'getStateOfComments'),
        );
    }

    public function getNumberOfComments($object, $id)
    {
        return $this->em->getRepository('ThiktakCommentBundle:Comment')->getNumberOfComments($object, $id);
    }

    public function getStateOfComments($object, $id)
    {
        return $this->em->getRepository('ThiktakCommentBundle:Comment')->getStateOfComments($object, $id);   
    }

    public function getName()
    {
        return 'comment_extension';
    }
}