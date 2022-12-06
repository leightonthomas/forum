<?php

declare(strict_types=1);

namespace Tests\Stub\Controller;

use App\Model\Attribute\Controller\JsonRoute;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GenericJsonRouteTestController
{
    #[JsonRoute]
    #[Route(name: 'test_invoke', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['hello!']);
    }
}
