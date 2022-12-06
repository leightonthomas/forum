<?php

declare(strict_types=1);

namespace App\EventSubscriber\Controller;

use App\Controller\GenericJsonRouteController;
use App\Model\Attribute\Controller\JsonRoute;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function array_slice;
use function count;
use function func_get_args;
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

        $arguments = $reflectedMethod->getParameters();
        $hasRequestArgument = (
            (count($arguments) > 0)
            && ($arguments[0]->hasType())
            && ($arguments[0]->getType()->getName() === Request::class)
        );

        $event->setController(
            function (Request $request) use ($controller, $hasRequestArgument): Response {
                $invalidResponse = $this->jsonRouteController->route($request);
                if ($invalidResponse instanceof Response) {
                    return $invalidResponse;
                }

                $rawArgs = func_get_args();
                if (($rawArgs[0] instanceof Request) && (! $hasRequestArgument)) {
                    $rawArgs = array_slice($rawArgs, 1);
                }

                // we don't know ahead of time what arguments the controller might have (other than request, of course)
                // so use this method to get them all dynamically and forward them on to the original controller
                return $controller(...$rawArgs);
            },
        );
    }
}
