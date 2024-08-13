<?php

namespace App\AeryApi\Repository;

use App\AeryApi\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param string $isbn
     * @return Book|null
     */
    public function findByIsbn(string $isbn): ?Book
    {
        return $this->createQueryBuilder('book')
            ->andWhere('book.isbn = :isbn')
            ->setParameter('isbn', $isbn)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Book[]
     */
    public function findByTitle(string $title): array
    {
        return $this->createQueryBuilder('book')
            ->andWhere('upper(book.title) = upper(:title)')
            ->setParameter('title', $title)
            ->orderBy('book.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Book[]
     */
    public function findByAuthor(string $author): array
    {
        return $this->createQueryBuilder('book')
            ->andWhere('upper(book.author) = upper(:author)')
            ->setParameter('author', $author)
            ->orderBy('book.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
