<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Book[]
     */
    public function findByNativeSQL()
    {
        $sql = '
            SELECT *, count
            FROM book
            INNER JOIN
                (SELECT book_id, COUNT(*) as count
                 FROM book_author GROUP BY book_id
                 HAVING COUNT(*) > 1) b
            ON book.id = b.book_id
            ORDER BY count ASC;
        ';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $stmt = $connection->prepare($sql);
            $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
        return $stmt->fetchAllAssociative();
    }

    /**
     * @return Book[]
     */
    public function findByDoctrineORM()
    {
        $queryBuilder = $this->createQueryBuilder('b');
        $query = $queryBuilder
            ->innerJoin('b.authors', 'authors')
            ->having('count(authors) > 1')
            ->groupBy('b.id')
            ->orderBy('count(authors)', 'ASC')
            ->getQuery();
        return $query->getResult();
    }

    /**
     * @return Book[]
     */
    public function filterBooksByParameters($parameters): array {
        $queryBuilder = $this->createQueryBuilder('book');
        $yearFrom = -1;
        foreach ($parameters as $key => $value) {
            if ($key === 'authors') {
                $queryBuilder
                    ->leftJoin('book.authors', 'authors')
                    ->andWhere('authors IN (:authors)')
                    ->setParameter(':authors', $value);
            } else {
                if ($key == "yearFrom") {
                    $yearFrom = $value;
                } else if($key == "yearTo") {
                    if ($yearFrom !== -1 && $yearFrom <= $value) {
                        $queryBuilder
                            ->andWhere('book.year >= :from')
                            ->andWhere('book.year <= :to')
                            ->setParameters(array('from' =>  $yearFrom,
                                                  'to' => $value)
                            );
                    }
                } else {
                    $queryBuilder
                        ->andWhere("book.$key LIKE :$key")
                        ->setParameter($key, "$value%");
                }
            }

        }
        $query = $queryBuilder->getQuery();
        return $query->execute();
    }
}
