<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Model\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findSorties(Search $search) : array
    {
        $queryBuilder = $this->createQueryBuilder('s');
            if ($search->getNom()){
                $queryBuilder
                    ->andWhere('s.nom like :val')
                    ->setParameter('val', "%".$search->getNom()."%");
            }
            if ($search->getCampus()){
                $queryBuilder
                    ->andWhere('s.campus =  :campus')
                    ->setParameter('campus', $search->getCampus());
            }
            if ($search->getDateDebut()){
                $queryBuilder
                    ->andWhere('s.dateHeureDebut >=  :dateDebut')
                    ->setParameter('dateDebut', $search->getDateDebut());

            }
            if ($search->getDateFin()){
                $queryBuilder
                    ->andWhere('s.dateHeureDebut <=  :dateFin')
                    ->setParameter('dateFin', $search->getDateFin());

            }
            if ($search->isSortiePassee()){
                $queryBuilder
                    ->andWhere('s.dateHeureDebut < current_Date()');
            }
//            ->andWhere('s.nom like :val')
//            ->setParameter('val', "%".$lib."%")
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
        $query  = $queryBuilder->getQuery();
        return $query->getResult();

    }
}
