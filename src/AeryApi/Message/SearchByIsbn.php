<?php

namespace App\AeryApi\Message;

readonly class SearchByIsbn
{
    /**
     * @param string $isbn
     */
    public function __construct(
        private string $isbn
    ) {
    }

    /**
     * @return string
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }
}
