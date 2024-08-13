<?php

namespace App\OpenLibrary\Service;

use App\AeryApi\Api\BookApiRequesterInterface;
use App\AeryApi\DataTransferObject\BookDTO;
use App\AeryApi\DataTransferObject\BookDTOFactory;
use App\OpenLibrary\Client\OpenLibraryClient;
use GuzzleHttp\Exception\GuzzleException;

class OpenLibraryService implements BookApiRequesterInterface
{
    public function __construct(
        private readonly OpenLibraryClient $client,
        private readonly BookDTOFactory $bookDTOFactory
    ) {
    }

    /**
     * @param string $title
     * @return array|BookDTO[]
     * @throws GuzzleException
     */
    public function getByTitle(string $title): array
    {
        $results = $this->client->getByTitle($title);

        return $this->processResults($results);
    }

    /**
     * @param string $author
     * @return array|BookDTO[]
     * @throws GuzzleException
     */
    public function getByAuthor(string $author): array
    {
        $results = $this->client->getByAuthor($author);

        return $this->processResults($results);
    }

    /**
     * @param string $isbn
     * @return array|BookDTO[]
     * @throws GuzzleException
     */
    public function getByISBN(string $isbn): array
    {
        $results = $this->client->getByIsbn($isbn);

        return $this->processResults($results);
    }

    /**
     * Some results from the OpenLibrary appear not to have ISBNs or author names (or several of them),
     * and the format of the results is often unusual (such as author_name being an array instead of a string).
     * These cases should be handled properly, but are not particularly interesting for a Symfony-assessment,
     * so I've opted to filter these cases out and/or ignore them in the interest of time.
     *
     * @param array $results
     * @return array
     */
    private function processResults(array $results): array
    {
        $results = array_filter($results, function ($result) {
            return array_key_exists('isbn', $result) && array_key_exists('author_name', $result);
        });

        $bookDTOs = [];
        foreach ($results as $result) {
            $bookDTOs[] = $this->bookDTOFactory->create($result['isbn'][0], $result['title'], $result['author_name'][0]);
        }

        return $bookDTOs;
    }
}
