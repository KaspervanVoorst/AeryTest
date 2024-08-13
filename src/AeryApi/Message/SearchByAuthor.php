<?php

namespace App\AeryApi\Message;

readonly class SearchByAuthor
{
    /**
     * @param string $author
     */
    public function __construct(
        private string $author
    ) {
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }
}
