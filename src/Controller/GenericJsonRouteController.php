<?php

declare(strict_types=1);

namespace App\Controller;

use App\EventSubscriber\Controller\JsonRouteSubscriber;
use App\Model\Attribute\Controller\JsonRoute;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function in_array;

/**
 * A controller used exclusively by {@see JsonRouteSubscriber} to perform generic request validation that should be
 * included on all {@see JsonRoute}s.
 */
class GenericJsonRouteController
{
    /**
     * Returns a JsonResponse if there's a problem with the request, otherwise returns null so the request can pass
     * to the intended controller.
     *
     * @param Request $request
     *
     * @return JsonResponse|null
     */
    public function route(Request $request): ?JsonResponse
    {
        $contentTypes = $request->getAcceptableContentTypes();
        if (
            (! in_array('*/*', $contentTypes))
            && (! in_array('application/json', $contentTypes))
        ) {
            // https://httpwg.org/specs/rfc9110.html#status.406 - we should provide acceptable content types
            return new JsonResponse(['application/json', '*/*'], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($request->getContentType() !== 'json') {
            $response = new JsonResponse(null, Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            // https://httpwg.org/specs/rfc9110.html#status.415 - we should set an Accept header on the response
            $response->headers->set('Accept', 'application/json');

            return $response;
        }

        return null;
    }
}
