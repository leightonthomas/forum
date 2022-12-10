<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler\Transformer;

use LeightonThomas\Validation\ValidatorFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Re-implementation while library bundle doesn't support Symfony 6 yet
 */
class ValidationCheckerPass implements CompilerPassInterface
{
    public const TAG = 'lt_validation.checker';

    public function process(ContainerBuilder $container): void
    {
        $factoryClass = ValidatorFactory::class;
        if (! $container->has($factoryClass)) {
            return;
        }

        $factory = $container->getDefinition(ValidatorFactory::class);

        foreach ($container->findTaggedServiceIds(self::TAG) as $serviceId => $_) {
            $factory->addMethodCall('register', [new Reference($serviceId)]);
        }
    }
}
