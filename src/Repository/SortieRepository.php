<?php

namespace App\Repository;

use App\Entity\Participant;
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
    public function findSortiesWithFilter(Search $search, Participant $participant) : array
    {
        $queryBuilder = $this->createQueryBuilder('s');
//            ne pas prendre les sorties terminées depuis plus de 30 jours, on les garde en historique mais pas en visu
            $queryBuilder
                ->andWhere('date_add(s.dateHeureDebut,30,\'DAY\')> current_Date()');
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
            if($search->isSortieOrganisateur()){
                $queryBuilder
                    ->andWhere('s.participantOrganisateur=:organisateur')
                    ->setParameter('organisateur',$participant->getId());
            }
            if($search->isSortieInscrit()){
                $queryBuilder
                    ->andWhere(':particpant MEMBER OF s.participants')
                    ->setParameter('particpant',$participant->getId());
            }
            if ($search->isSortiePasInscrit()){
                $queryBuilder
                    ->andWhere(':participant NOT MEMBER OF s.participants')
                    ->setParameter('participant',$participant->getId());
            }
//            ->andWhere('s.nom like :val')
//            ->setParameter('val', "%".$lib."%")
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
        $query  = $queryBuilder->getQuery();
        return $query->getResult();

    }
}
