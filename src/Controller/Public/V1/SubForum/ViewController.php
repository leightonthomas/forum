<?php

declare(strict_types=1);

namespace App\Controller\Public\V1\SubForum;

use App\Crypto\Encryption\Entity\AccountEncryptor;
use App\Crypto\Encryption\Entity\Thread\ThreadEncryptor;
use App\Model\Attribute\Controller\JsonRoute;
use App\Model\Entity\Thread\Thread;
use App\Model\Exception\Crypto\Encryption\EncryptionMethod\DecryptionFailure;
use App\Model\Repository\Entity\SubForumRepository;
use App\Model\Repository\Entity\Thread\ThreadRepository;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_map;
use function is_numeric;
use function max;
use function min;

class ViewController
{
    public function __construct(
        private readonly ThreadRepository $threadRepository,
        private readonly AccountEncryptor $accountEncryptor,
        private readonly LoggerInterface $logger,
        private readonly SubForumRepository $subForumRepository,
        private readonly ThreadEncryptor $threadEncryptor,
    ) { }

    // TODO: test remaining stuff added in this PR
    // TODO: add e2e test for subforum list (+ unit for controller if necessary?)

    // TODO: then... new stuff
    //       editing a post adds a ThreadEvent AND adds a new PostHistory
    //       the post content should be stored in latest PostHistory

    #[Route(path: '/public/v1/subforum/{subforum}', name: 'public_subforum_view', methods: ['GET'])]
    #[JsonRoute]
    public function __invoke(Request $request, string $subforum): JsonResponse
    {
        $page = $request->query->get('page', 1);
        if (! is_numeric($page)) {
            $page = 1;
        }

        $page = max(min((int) $page, 10000), 1);

        $limit = $request->query->get('limit', 25);
        if (! is_numeric($limit)) {
            $limit = 25;
        }

        $limit = max(min((int) $limit, 50), 1);

        $subforum = $this->subForumRepository->findById($subforum);
        if ($subforum === null) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        // TODO add page total, current page number, number of items

        try {
            return new JsonResponse(
                [
                    'id' => $subforum->getId(),
                    'name' => $subforum->getName(),
                    'threads' => array_map(
                        function (Thread $thread): array {
                            $decryptedAuthor = $this->accountEncryptor->decrypt($thread->getAuthor());
                            $decryptedThread = $this->threadEncryptor->decrypt($thread);

                            return [
                                'id' => $thread->getId(),
                                'name' => $decryptedThread->getString(),
                                'createdAt' => $thread->getCreatedAt()->format(DateTimeInterface::ATOM),
                                'author' => [
                                    'id' => $thread->getAuthor()->getId(),
                                    'username' => $decryptedAuthor->username->getString(),
                                ],
                            ];
                        },
                        $this->threadRepository->getPageForSubForum($subforum, $page, $limit),
                    ),
                ],
            );
        } catch (DecryptionFailure $e) {
            $this->logger->critical("Could not view SubForum; {$e->getMessage()}");

            return new JsonResponse(
                ['An error has occurred. Please try again later.'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
