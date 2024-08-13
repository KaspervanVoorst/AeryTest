<?php

namespace App\AeryApi\Api;

use App\AeryApi\DataTransferObject\BookDTO;

interface BookApiRequesterInterface
{
    /**
     * @param string $title
     * @return BookDTO[]
     */
    public function getByTitle(string $title): array;

    /**
     * @param string $author
     * @return BookDTO[]
     */
    public function getByAuthor(string $author): array;

    /**
     * @param string $isbn
     * @return BookDTO[]
     */
    public function getByISBN(string $isbn): array;
}
