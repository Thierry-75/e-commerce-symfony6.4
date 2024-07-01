<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProduitsPaginate(int $page,string $slug, int $limit = 6): array
    {
        $limit = abs($limit);

        $result = [];
        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('c','p')
                ->from('App\Entity\Product', 'p')
                ->join('p.categorie','c')
                ->andWhere('c.slug = :val')
                ->setParameter('val',$slug)
                ->setMaxResults($limit)
                ->setFirstResult(($page * $limit) - $limit)
                ;

                $paginator = new Paginator($query);
                $data = $paginator->getQuery()->getResult();

                if(empty($data)){
                    return $result;
                }

                // nb pages
                $pages = ceil($paginator->count() / $limit);
                // initialisation
                $result['data'] = $data;
                $result['pages']= $pages;
                $result['page']= $page;
                $result['limit']= $limit;
        return $result;
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
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

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
