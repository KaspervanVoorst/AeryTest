<?php

namespace App\AeryApi\Handler;

use App\AeryApi\DataTransferObject\BookDTO;
use App\AeryApi\Entity\Book;
use App\AeryApi\Message\SearchByAuthor;
use App\AeryApi\Message\SearchByIsbn;
use App\AeryApi\Message\SearchByTitle;
use App\AeryApi\Repository\BookRepository;
use App\OpenLibrary\Service\OpenLibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class SearchHandler
{
    public function __construct(
        private OpenLibraryService $openLibraryService,
        private EntityManagerInterface $entityManager,
        private BookRepository $bookRepository,
        private LockFactory $lockFactory
    ) {
    }

    /**
     * @param SearchByAuthor $searchByAuthor
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[AsMessageHandler]
    public function handleSearchByAuthor(SearchByAuthor $searchByAuthor): void
    {
        $lock = $this->lockFactory->createLock($searchByAuthor->getAuthor());

        if ($lock->acquire() === true) {
            $bookDTOs = $this->openLibraryService->getByAuthor($searchByAuthor->getAuthor());

            $this->saveBookResults($bookDTOs);

            $lock->release();
        }
    }

    /**
     * @param SearchByTitle $searchByTitle
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[AsMessageHandler]
    public function handleSearchByTitle(SearchByTitle $searchByTitle): void
    {
        $lock = $this->lockFactory->createLock($searchByTitle->getTitle());
        if ($lock->acquire() === true) {
            $bookDTOs = $this->openLibraryService->getByTitle($searchByTitle->getTitle());

            $this->saveBookResults($bookDTOs);

            $lock->release();
        }
    }

    /**
     * @param SearchByIsbn $searchByIsbn
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[AsMessageHandler]
    public function handleSearchByIsbn(SearchByIsbn $searchByIsbn): void
    {
        $lock = $this->lockFactory->createLock($searchByIsbn->getIsbn());
        if ($lock->acquire() === true) {
            $bookDTOs = $this->openLibraryService->getByIsbn($searchByIsbn->getIsbn());

            $this->saveBookResults($bookDTOs);

            $lock->release();
        }
    }

    /**
     * @param BookDTO[] $bookDTOs
     */
    private function saveBookResults(array $bookDTOs): void
    {
        foreach ($bookDTOs as $bookDTO) {
            $book = $this->bookRepository->findByIsbn($bookDTO->getIsbn());

            if ($book === null) {
                $book = new Book();
                $book->setIsbn($bookDTO->getIsbn());
            }
            $book->setTitle($bookDTO->getTitle());
            $book->setAuthor($bookDTO->getAuthor());

            $this->entityManager->persist($book);
            $this->entityManager->flush();
        }
    }
}
