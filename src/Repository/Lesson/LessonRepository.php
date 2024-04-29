<?php

namespace App\Repository\Lesson;

use App\Entity\Lesson\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Lesson>
 *
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    public function save (Lesson $lesson, bool $flush = false): void
    {
        $this->getEntityManager()->persist($lesson);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return PaginationInterface Returns an array of Lesson objects
     */
    public function findSimilar(Lesson $lesson, PaginatorInterface $paginator, $page = 1, $perPage = 10): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('l')
            ->andWhere('l.category = :category')
            ->andWhere('l.id != :id')
            ->setParameter('category', $lesson->getCategory())
            ->setParameter('id', $lesson->getId())
            ->orderBy('l.id', 'ASC');

        return $paginator->paginate(
            $queryBuilder,
            $page,
            $perPage
        );
    }

//    public function findOneBySomeField($value): ?Lesson
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
