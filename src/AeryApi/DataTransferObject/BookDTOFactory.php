<?php

namespace App\AeryApi\DataTransferObject;

use App\AeryApi\Entity\Book;

class BookDTOFactory
{
    /**
     * @param string $isbn
     * @param string $title
     * @param string $author
     * @return BookDTO
     */
    public function create(string $isbn, string $title, string $author): BookDTO
    {
        return new BookDTO($isbn, $title, $author);
    }

    /**
     * @param Book $book
     * @return BookDTO
     */
    public function createFromBook(Book $book): BookDTO
    {
        return $this->create($book->getIsbn(), $book->getTitle(), $book->getAuthor());
    }
}
