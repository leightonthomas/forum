<?php

declare(strict_types=1);

namespace App\Controller\Public\V1\SubForum;

use App\Model\Attribute\Controller\JsonRoute;
use App\Model\Entity\SubForum;
use App\Model\Repository\Entity\SubForumRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use function array_map;

class ListController
{
    public function __construct(
        private readonly SubForumRepository $subforumRepository,
    ) { }

    #[Route(path: '/public/v1/subforum', name: 'public_subforum_list', methods: ['GET'])]
    #[JsonRoute]
    public function __invoke(): JsonResponse
    {
        $forums = $this->subforumRepository->list();

        return new JsonResponse(
            array_map(
                fn(SubForum $forum): array => [
                    'id' => $forum->getId(),
                    'name' => $forum->getName(),
                ],
                $forums,
            ),
        );
    }
}
