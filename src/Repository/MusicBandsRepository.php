<?php

namespace App\Repository;

use App\Entity\MusicBands;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MusicBands>
 *
 * @method MusicBands|null find($id, $lockMode = null, $lockVersion = null)
 * @method MusicBands|null findOneBy(array $criteria, array $orderBy = null)
 * @method MusicBands[]    findAll()
 * @method MusicBands[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusicBandsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MusicBands::class);
    }

//    /**
//     * @return MusicBands[] Returns an array of MusicBands objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MusicBands
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
