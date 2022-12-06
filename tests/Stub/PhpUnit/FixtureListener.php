<?php

declare(strict_types=1);

namespace Tests\Stub\PhpUnit;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade as RunnerFacade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Tests\Stub\PhpUnit\Subscriber\FixtureSubscriber;

class FixtureListener implements Extension
{
    public function bootstrap(Configuration $configuration, RunnerFacade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscribers(new FixtureSubscriber($parameters->get('directory')));
    }
}
