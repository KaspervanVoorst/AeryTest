<?php

namespace App\AeryApi\Service;

use App\AeryApi\Message\SearchByAuthor;
use App\AeryApi\Message\SearchByIsbn;
use App\AeryApi\Message\SearchByTitle;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class SearchDispatcher
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    /**
     * @param string $author
     * @return Envelope
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    public function searchByAuthor(string $author): Envelope
    {
        return $this->messageBus->dispatch(new SearchByAuthor($author));
    }

    /**
     * @param string $title
     * @return Envelope
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    public function searchByTitle(string $title): Envelope
    {
        return $this->messageBus->dispatch(new SearchByTitle($title));
    }

    /**
     * @param string $isbn
     * @return Envelope
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    public function searchByIsbn(string $isbn): Envelope
    {
        return $this->messageBus->dispatch(new SearchByIsbn($isbn));
    }
}
