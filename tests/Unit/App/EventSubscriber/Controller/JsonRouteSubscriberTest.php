<?php

declare(strict_types=1);

namespace Tests\Unit\App\EventSubscriber\Controller;

use App\Controller\GenericJsonRouteController;
use App\EventSubscriber\Controller\JsonRouteSubscriber;
use App\Model\Attribute\Controller\JsonRoute;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class JsonRouteSubscriberTest extends TestCase
{
    private MockObject&GenericJsonRouteController $controller;
    private MockObject&KernelInterface $kernel;
    private JsonRouteSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->controller = $this
            ->getMockBuilder(GenericJsonRouteController::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->subscriber = new JsonRouteSubscriber($this->controller);
    }

    #[Test]
    public function itWillBeSubscribedToTheCorrectEvents(): void
    {
        self::assertSame(
            [
                KernelEvents::CONTROLLER => 'onController',
            ],
            JsonRouteSubscriber::getSubscribedEvents(),
        );
    }

    #[Test]
    public function itWillNotAlterControllerIfControllerNotAnObject(): void
    {
        $request = new Request();
        $event = new ControllerEvent($this->kernel, fn() => new JsonResponse(['text' => 'hi']), $request, null);

        $this->controller
            ->expects(self::never())
            ->method('route')
        ;

        $this->subscriber->onController($event);

        $result = $event->getController()($request, 'hi');

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame('{"text":"hi"}', $result->getContent());
    }

    #[Test]
    public function itWillNotAlterControllerIfControllerDoesNotHaveInvokeMethod(): void
    {
        $originalController = new class {
            public function route(): JsonResponse
            {
                return new JsonResponse(['text' => 'hi']);
            }
        };
        $request = new Request();
        $event = new ControllerEvent($this->kernel, $originalController->route(...), $request, null);

        $this->controller
            ->expects(self::never())
            ->method('route')
        ;

        $this->subscriber->onController($event);

        $result = $event->getController()($request, 'hi');

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame('{"text":"hi"}', $result->getContent());
    }

    #[Test]
    public function itWillNotAlterControllerIfControllerInvokeMethodDoesNotHaveJsonRouteAttribute(): void
    {
        $originalController = new class {
            public function __invoke(): JsonResponse
            {
                return new JsonResponse(['text' => 'hi']);
            }
        };
        $request = new Request();
        $event = new ControllerEvent($this->kernel, $originalController, $request, null);

        $this->controller
            ->expects(self::never())
            ->method('route')
        ;

        $this->subscriber->onController($event);

        $result = $event->getController()($request, 'hi');

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame('{"text":"hi"}', $result->getContent());
    }

    #[Test]
    public function ifJsonRouteControllerReturnsResponseItWillNotCallOriginalController(): void
    {
        $originalController = new class {
            public bool $called = false;

            #[JsonRoute]
            public function __invoke(): JsonResponse
            {
                $this->called = true;

                return new JsonResponse(['text' => 'hi']);
            }
        };
        $request = new Request();
        $event = new ControllerEvent($this->kernel, $originalController, $request, null);

        $this->controller
            ->expects(self::once())
            ->method('route')
            ->with($request)
            ->willReturn(new JsonResponse(['text' => 'other']))
        ;

        $this->subscriber->onController($event);

        $result = $event->getController()($request, 'hi');

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame('{"text":"other"}', $result->getContent());
        self::assertFalse($originalController->called);
    }

    #[Test]
    public function ifJsonRouteControllerReturnsNullItWillCallOriginalController(): void
    {
        $originalController = new class {
            public bool $called = false;

            #[JsonRoute]
            public function __invoke(Request $request, string $text): JsonResponse
            {
                $this->called = true;

                return new JsonResponse(['text' => $text]);
            }
        };
        $request = new Request();
        $event = new ControllerEvent($this->kernel, $originalController, $request, null);

        $this->controller
            ->expects(self::once())
            ->method('route')
            ->with($request)
            ->willReturn(null)
        ;

        $this->subscriber->onController($event);

        $result = $event->getController()($request, 'hi');

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame('{"text":"hi"}', $result->getContent());
        self::assertTrue($originalController->called);
    }

    #[Test]
    public function itWillNotPassRequestIfFirstArgumentOfOriginalControllerIsNotRequest(): void
    {
        $originalController = new class {
            public bool $called = false;

            #[JsonRoute]
            public function __invoke(string $text): JsonResponse
            {
                $this->called = true;

                return new JsonResponse(['text' => $text]);
            }
        };
        $request = new Request();
        $event = new ControllerEvent($this->kernel, $originalController, $request, null);

        $this->controller
            ->expects(self::once())
            ->method('route')
            ->with($request)
            ->willReturn(null)
        ;

        $this->subscriber->onController($event);

        $result = $event->getController()($request, 'hi');

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame('{"text":"hi"}', $result->getContent());
        self::assertTrue($originalController->called);
    }
}
