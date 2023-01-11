<?php

declare(strict_types=1);

namespace App\EventSubscriber\Controller;

use App\Controller\GenericJsonRouteController;
use App\Model\Attribute\Controller\JsonRoute;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function count;
use function get_class;
use function is_object;
use function method_exists;

/**
 * Perform generic validation on {@see JsonRoute} attributed routes before running actual controller logic.
 */
class JsonRouteSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
        ];
    }

    public function __construct(
        private readonly GenericJsonRouteController $jsonRouteController,
    ) { }

    public function onController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if ((! is_object($controller)) || (! method_exists($controller, '__invoke'))) {
            return;
        }

        $controllerClass = get_class($controller);

        try {
            $reflectedClass = new ReflectionClass($controllerClass);
        } catch (ReflectionException) {
            throw new RuntimeException("Could not instantiate ReflectionClass for controller '$controllerClass'");
        }

        try {
            $reflectedMethod = $reflectedClass->getMethod('__invoke');
        } catch (ReflectionException) {
            throw new RuntimeException("Could not find method '__invoke' for controller '$controllerClass'");
        }

        if (count($reflectedMethod->getAttributes(JsonRoute::class)) <= 0) {
            return;
        }

        $invalidResponse = $this->jsonRouteController->route($event->getRequest());
        if ($invalidResponse instanceof Response) {
            $event->setController(fn(): Response => $invalidResponse);
        }
    }
}
