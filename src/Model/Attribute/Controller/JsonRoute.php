<?php

declare(strict_types=1);

namespace App\Model\Attribute\Controller;

use App\EventSubscriber\Controller\JsonRouteSubscriber;
use Attribute;

/**
 * Marks a route as being JSON based, and will cause the {@see JsonRouteSubscriber} to perform standardised checks for
 * things like Content-Type and Accept headers/
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class JsonRoute
{
}
