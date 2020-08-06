<?php

namespace App\Repository;

use App\Entity\Agenda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agenda|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agenda|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agenda[]    findAll()
 * @method Agenda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgendaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agenda::class);
    }


    public function findTheThreeLastRendezVous()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.Date >= :today')
            ->setParameter('today', date('Y-m-d h:m:s'))
            ->orderBy('a.Date', 'ASC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
}
