<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use League\Csv\Reader;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns an array of Product objects
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

    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return array
     */
    public function findProductIds()
    {
        $products = $this->findAll();
        $productIds = [];
        foreach ($products as $product) {
            $productIds[] = $product->getId();
        }

        return $productIds;
    }


    /**
     * @param array $productIdsToUpdate
     * @param Reader $productRecord
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateProductsFromCsvFile(array $productIdsToUpdate, Reader $productCsv)
    {
        $products = $this->findBy(['id' => $productIdsToUpdate]);
        $entityManager = $this->getEntityManager();

        foreach ($products as $index => $product) {
            $productName = $productCsv->fetchOne($index)['Name'];
            $productPrice = $productCsv->fetchOne($index)['Price'];
            $product->setName($productName);
            $product->setPrice($productPrice);
            $entityManager->persist($product);
            $entityManager->flush();
        }
    }

    public function selectProductsIdsNamesAndPrices()
    {
        return $this->createQueryBuilder('p')
            ->select('p.id', 'p.name', 'p.price')
            ->getQuery()
            ->getResult()
        ;
    }
}
