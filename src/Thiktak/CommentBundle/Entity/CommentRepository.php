<?php

namespace Thiktak\CommentBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends EntityRepository
{
    public function getNumberOfComments($object, $id)
    {
        return $this->createQueryBuilder('c')
                    ->select('COUNT(c.id)')
                    ->andWhere('c.type = :type')
                    ->andWhere('c.typeId = :typeId')
                    ->setParameter('type', $object)
                    ->setParameter('typeId', $id)
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getStateOfComments($object, $id)
    {
        $ret = $this->createQueryBuilder('c')
                    ->select('c.state')
                    ->andWhere('c.type = :type')
                    ->andWhere('c.typeId = :typeId')
                    ->andWhere('c.state IS NOT NULL')
                    ->setParameter('type', $object)
                    ->setParameter('typeId', $id)
                    ->orderBy('c.dateCreated', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult();
        return count($ret) ? $ret[0]['state'] : null;
    }
}
