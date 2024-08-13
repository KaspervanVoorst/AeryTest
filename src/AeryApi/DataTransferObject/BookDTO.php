<?php

namespace App\AeryApi\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

class BookDTO
{
    /**
     * @var string|null
     */
    #[Assert\Length(min: 0, max: 13)]
    #[Assert\Regex('^(97(8|9))?\d{9}(\d|X)$^')]
    public ?string $isbn = null;

    /**
     * @var string|null
     */
    #[Assert\Length(min: 0, max: 255)]
    public ?string $title = null;

    /**
     * @var string|null
     */
    #[Assert\Length(min: 0, max: 255)]
    public ?string $author = null;

    /**
     * @param string|null $isbn
     * @param string|null $title
     * @param string|null $author
     */
    public function __construct(?string $isbn = null, ?string $title = null, ?string $author = null)
    {
        $this->isbn = $isbn;
        $this->title = $title;
        $this->author = $author;
    }

    /**
     * @return string|null
     */
    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }
}
