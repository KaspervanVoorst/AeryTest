<?php

namespace App\OpenLibrary\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenLibraryClient
{
    public const string BASE_URL = 'https://openlibrary.org/';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => self::BASE_URL]);
    }

    /**
     * @throws GuzzleException
     */
    public function getByTitle(string $title): array
    {
        $response = $this->client->request('GET', sprintf('search.json?title=%s&limit=1000', $title), ['http_errors' => true])->getBody();

        $responseContents = \json_decode($response->getContents(), true);

        return $responseContents['docs'];
    }

    /**
     * @throws GuzzleException
     */
    public function getByAuthor(string $author): array
    {
        $response = $this->client->request('GET', sprintf('search.json?author=%s&limit=1000', $author), ['http_errors' => true])->getBody();

        $responseContents = \json_decode($response->getContents(), true);

        return $responseContents['docs'];
    }

    /**
     * @throws GuzzleException
     */
    public function getByIsbn(string $isbn): array
    {
        $response = $this->client->request('GET', sprintf('search.json?isbn=%s&limit=1000', $isbn), ['http_errors' => true])->getBody();

        $responseContents = \json_decode($response->getContents(), true);

        return $responseContents['docs'];
    }
}
