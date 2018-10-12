<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProductPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductPhoto[]    findAll()
 * @method ProductPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductPhotoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductPhoto::class);
    }

    /**
     * @return ProductPhoto[] Returns an array of ProductPhoto objects
     */
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneBySomeField($value): ?ProductPhoto
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param Product $product
     * @return ProductPhoto[]
     */
    public function findProductPhotos(Product $product)
    {
        return $this->createQueryBuilder('product_photo')
            ->andWhere('product_photo.product = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->execute();
    }
}
