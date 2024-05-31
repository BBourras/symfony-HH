<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function createAllPublishedOrderedByNewestQuery(?Tag $tag) : Query
    {
       $queryBuilder = $this->createQueryBuilder('p')
           ->andWhere('p.publishedAt IS NOT NULL')
           ->andWhere('p.publishedAt <= :now')
           ->addSelect('t')
           ->leftJoin('p.tags', 't')
           ->orderBy('p.publishedAt', 'DESC')
           ->setParameter(':now', new \DateTimeImmutable());
       if ($tag !== null){
           $queryBuilder->andWhere(':tag MEMBER OF p.tags')
               ->setParameter(':tag', $tag);
       }
           return $queryBuilder->getQuery();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function FindOneByPublishDateAndSlug(string $date, string $slug): ?Post
    {
        return $this
            ->createQueryBuilder('p')
//            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('DATE(p.publishedAt) = :date')
            ->andWhere('p.slug = :slug')
            ->setParameters([
                'date' => $date,
                'slug' => $slug,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
    * @return Post[] Returns an array of Post objects
    */
    public function findSimilar(Post $post, int $maxResults = 5): array
    {
        return $this
           ->createQueryBuilder('p')
           ->join('p.tags', 't')
           ->addSelect('t, COUNT(t.id) AS HIDDEN numberOfTags')
           ->andWhere('t IN (:tags)')
           ->andWhere('p != :post')
           ->setParameters([
            'tags' => $post->getTags(),
            'post' => $post
           ])
           ->groupBy('p.id')
           ->addOrderBy('numberOfTags', 'DESC')
           ->addOrderBy('p.publishedAt', 'DESC')
           ->setMaxResults($maxResults)
           ->getQuery()
           ->getResult()
       ;
    }

    public function findMostCommented(int $maxResults): array
    {
        return $this
           ->createQueryBuilder('p')
           ->join('p.comments', 'c')
           ->addSelect('COUNT(c) AS HIDDEN numberOfComments')
           ->andWhere('c.isActive = true')
           ->groupBy('p')
           ->addOrderBy('numberOfComments', 'DESC')
           ->addOrderBy('p.publishedAt', 'DESC')
           ->setMaxResults($maxResults)
           ->getQuery()
           ->getResult()
       ;
    }


//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
