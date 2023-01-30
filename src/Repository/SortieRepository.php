<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;

use App\filtre\recherche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\DateTime;


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

    public function listeSortiesDefaut(Participant $user): array
    {
        $campus = $user->getCampus();

        return $this->createQueryBuilder('s')
            ->andWhere('s.campus = :campus')
            ->setParameter('campus', $campus)
            ->andWhere('s.dateHeureDebut >= :date')
            ->setParameter('date', date('Y-m-d H:i:s'))
            ->andWhere('s.organisateur = :user OR s.etat != 1')
            ->setParameter('user', $user)->getQuery()->getResult();

        //  $paginator = new Paginator($qb->getQuery());

        // return $paginator;
    }

    public function filtre(recherche $recherche, Participant $user)
    {

        $query = $this->createQueryBuilder('s');

        if ($recherche->getSortiePassee()) {
            $query->andWhere('s.etat = 5');
        }

        if ($recherche->getCampus()) {
            $query->andWhere('s.campus = :campus')
                ->setParameter('campus', $recherche->getCampus());
        }

        if ($recherche->getDateDebut() && $recherche->getDateFin()) {
            $query->andWhere('s.dateLimiteInscription >?1 ')
                ->andWhere(' s.dateLimiteInscription< ?2 ')
                ->setParameter(1, $recherche->getDateDebut())
                ->setParameter(2, $recherche->getDateFin());
        }

        if ($recherche->getNom()) {
            $query->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $recherche->getNom() . '%');
        }

        if ($recherche->getSortieOrganisateur()) {
            $query->andWhere('s.organisateur = :user')
                ->setParameter('user', $user);
        }

        if ($recherche->getSortieInscrit()) {
            $query->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $user);
        }

        if ($recherche->getSortieNonInscrit()) {
            $query->andWhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $user);
        }


        return $query->getQuery()->getResult();

    }






}
