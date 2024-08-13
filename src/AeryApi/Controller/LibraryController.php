<?php

namespace App\AeryApi\Controller;

use App\AeryApi\DataTransferObject\BookDTO;
use App\AeryApi\DataTransferObject\BookDTOFactory;
use App\AeryApi\Entity\Book;
use App\AeryApi\Repository\BookRepository;
use App\AeryApi\Service\SearchDispatcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

class LibraryController extends AbstractController
{

    /**
     * @param BookRepository $bookRepository
     * @param BookDTOFactory $bookDTOFactory
     * @param SearchDispatcher $searchDispatcher
     * @param RateLimiterFactory $anonymousLimiter
     */
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly BookDTOFactory $bookDTOFactory,
        private readonly SearchDispatcher $searchDispatcher,
        private RateLimiterFactory $anonymousLimiter
    ) {
    }

    /**
     * @param Request $request
     * @param string $title
     * @return JsonResponse
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    #[Route('/api/v1/library/title/{title}', name: 'library_title', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all books found in the database with the given title.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(type: BookDTO::class),
            ),
            example: [new BookDTO('1611748747', 'The Lord of the Rings', 'J.R.R. Tolkien')]
        )
    )]
    #[OA\Parameter(
        name: 'title',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'The+Adventures+of+Jave+Dohnson'
    )]
    public function findByTitle(Request $request, string $title): JsonResponse
    {
        $this->enforceRateLimit($request);

        $this->searchDispatcher->searchByTitle($title);

        $filteredTitle = $this->filterSearchQuery($title);
        $bookEntities = $this->bookRepository->findByTitle($filteredTitle);

        return $this->createResponseFromEntities($bookEntities);
    }

    /**
     * @param Request $request
     * @param string $author
     * @return JsonResponse
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    #[Route('/api/v1/library/author/{author}', name: 'library_author', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all books found in the database by the given author.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(type: BookDTO::class)
            ),
            example: [new BookDTO('1611748747', 'The Lord of the Rings', 'J.R.R. Tolkien')]
        )
    )]
    #[OA\Parameter(
        name: 'author',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'Dave+Johnson',
    )]
    public function findByAuthor(Request $request, string $author): JsonResponse
    {
        $this->enforceRateLimit($request);

        $this->searchDispatcher->searchByAuthor($author);

        $filteredAuthor = $this->filterSearchQuery($author);
        $bookEntities = $this->bookRepository->findByAuthor($filteredAuthor);

        return $this->createResponseFromEntities($bookEntities);
    }

    /**
     * @param Request $request
     * @param string $isbn
     * @return JsonResponse
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    #[Route('/api/v1/library/isbn/{isbn}', name: 'library_isbn', requirements: ['isbn' => '(97(8|9))?\d{9}(\d|X)'], methods: ['GET'], priority: 1)]
    #[OA\Response(
        response: 200,
        description: 'Returns the book with the given ISBN (a unique identifier) if it exists.',
        content: new OA\JsonContent(
            ref: new Model(type: BookDTO::class),
            type: 'object',
            example: new BookDTO('1611748747', 'The Lord of the Rings', 'J.R.R. Tolkien')
        )
    )]
    #[OA\Parameter(
        name: 'isbn',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', pattern: '(97(8|9))?\d{9}(\d|X)')
    )]
    public function findByIsbn(Request $request, string $isbn): JsonResponse
    {
        $this->enforceRateLimit($request);

        $this->searchDispatcher->searchByIsbn($isbn);

        $bookEntity = $this->bookRepository->findByIsbn($isbn);

        return $this->json($bookEntity);
    }

    /**
     * @param Request $request
     * @param string $isbn
     * @return JsonResponse
     */
    #[Route('/api/v1/library/isbn/{isbn}', name: 'library_isbn_invalid', methods: ['GET'], priority: 0)]
    #[OA\Response(
        response: 400,
        description: 'Returns a 400 BAD REQUEST error when the given ISBN is invalid.',
        content: new OA\JsonContent(
            type: 'string',
        )
    )]
    #[OA\Parameter(
        name: 'isbn',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', pattern: '?!(97(8|9))?\d{9}(\d|X)'),
        example: 'Invalid ISBN: 123'
    )]
    public function invalidIsbn(Request $request, string $isbn): JsonResponse
    {
        $this->enforceRateLimit($request);

        return $this->json(sprintf('Invalid ISBN: %s', $isbn), 400);
    }

    /**
     * @param Book[] $bookEntities
     * @return JsonResponse
     */
    private function createResponseFromEntities(array $bookEntities): JsonResponse
    {
        $bookDTOs = [];
        foreach ($bookEntities as $bookEntity) {
            $bookDTOs[] = $this->bookDTOFactory->createFromBook($bookEntity);
        }

        return $this->json($bookDTOs);
    }

    /**
     * OpenLibrary uses Solr search queries, where words are separated by + in a search string.
     * Implementing something similar seems out of the scope of this assessment, so hack the + signs into spaces instead.
     *
     * @param string $query
     * @return string
     */
    private function filterSearchQuery(string $query): string
    {
        return preg_replace('/[^a-zA-Z0-9.-]+/', ' ', $query);
    }

    /**
     * This is not the right place to implement rate limiting - I'm just ticking the box with this one.
     *
     * @param Request $request
     */
    private function enforceRateLimit(Request $request): void
    {
        $limiter = $this->anonymousLimiter->create($request->getClientIp());

        if ($limiter->consume()->isAccepted() === false) {
            throw new TooManyRequestsHttpException();
        }
    }
}
