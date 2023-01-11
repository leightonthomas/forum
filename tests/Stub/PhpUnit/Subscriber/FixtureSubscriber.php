<?php

declare(strict_types=1);

namespace Tests\Stub\PhpUnit\Subscriber;

use Closure;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Exception\TableExistsException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Stub\Attribute\Fixture;
use Tests\Stub\Attribute\FixtureDirectory;
use function array_chunk;
use function array_key_exists;
use function file_exists;
use function get_class;
use function is_array;
use function is_callable;
use function ltrim;
use function rtrim;
use function sprintf;
use function str_ends_with;
use function str_starts_with;
use function substr;

/**
 * @psalm-type FixtureCallable = callable(ContainerInterface):array
 */
class FixtureSubscriber implements PreparedSubscriber
{
    /** @var array<string, FixtureCallable> */
    private array $fixtures = [];
    private bool $alreadyCreatedSchema = false;

    public function __construct(
        private readonly string $rootFixtureDirectory
    ) { }

    /**
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ToolsException
     */
    public function notify(Prepared $event): void
    {
        $test = $event->test();
        if (! ($test instanceof TestMethod)) {
            return;
        }

        try {
            $reflectedClass = new ReflectionClass($test->className());
        } catch (ReflectionException $e) {
            throw new RuntimeException("Could not reflect '{$test->className()}' - {$e->getMessage()}", 0, $e);
        }

        $fixtureDirectoryAttributeRaw = $reflectedClass->getAttributes(FixtureDirectory::class)[0] ?? null;
        if ($fixtureDirectoryAttributeRaw === null) {
            return;
        }

        if (! $reflectedClass->isSubclassOf(KernelTestCase::class)) {
            throw new RuntimeException("TestCases tagged with 'FixtureDirectory' must extend 'KernelTestCase'");
        }

        /** @var FixtureDirectory $fixtureDirectoryAttribute */
        $fixtureDirectoryAttribute = $fixtureDirectoryAttributeRaw->newInstance();

        try {
            $reflectedTestMethod = $reflectedClass->getMethod($test->methodName());
        } catch (ReflectionException $e) {
            throw new RuntimeException(
                "Could not reflect '{$test->className()}::{$test->methodName()}' - {$e->getMessage()}",
                0,
                $e,
            );
        }

        $fixtureAttributeRaw = $reflectedTestMethod->getAttributes(Fixture::class)[0] ?? null;
        if ($fixtureAttributeRaw === null) {
            return;
        }

        /** @var Fixture $fixtureAttribute */
        $fixtureAttribute = $fixtureAttributeRaw->newInstance();
        if (! str_ends_with($fixtureAttribute->fixtureName, '.php')) {
            throw new RuntimeException("Fixtures must be raw .php files that return a callable.");
        }

        $fixturePath = sprintf(
            '%s/%s/%s',
            rtrim($this->rootFixtureDirectory, '/'),
            rtrim(ltrim($fixtureDirectoryAttribute->directory, '/'), '/'),
            $fixtureAttribute->fixtureName,
        );
        if (str_starts_with($fixtureAttribute->fixtureName, './')) {
            $fixturePath = sprintf(
                '%s/%s',
                rtrim($this->rootFixtureDirectory, '/'),
                substr($fixtureAttribute->fixtureName, 2),
            );
        }

        $fixtureCallable = $this->resolveFixture($fixturePath);

        $container = $this->getContainer($reflectedClass);
        $entityManager = $container->get('doctrine.orm.default_entity_manager');

        if (! $this->alreadyCreatedSchema) {
            $tool = new SchemaTool($entityManager);
            try {
                $tool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
            } catch (ToolsException $e) {
                if (! ($e->getPrevious() instanceof TableExistsException)) {
                    throw $e;
                }
            }

            $this->alreadyCreatedSchema = true;
        }

        $purger = new ORMPurger($entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);

        $entityManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        $purger->purge();
        $entityManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS = 1');

        $fixtureResult = $fixtureCallable($container);
        if (! is_array($fixtureResult)) {
            throw new RuntimeException("Fixture callbacks must return an array of objects.");
        }

        foreach (array_chunk($fixtureResult, 50) as $entities) {
            foreach ($entities as $entity) {
                $entityManager->persist($entity);
            }

            $entityManager->flush();
        }
    }

    /**
     * @param string $fullPath
     *
     * @return FixtureCallable
     */
    private function resolveFixture(string $fullPath): callable
    {
        if (! array_key_exists($fullPath, $this->fixtures)) {
            if (! file_exists($fullPath)) {
                throw new RuntimeException("Could not find fixture '$fullPath'");
            }

            $result = require_once $fullPath;
            if (! (is_callable($result) || ($result instanceof Closure))) {
                $type = get_class($result);

                throw new RuntimeException("Fixture '$fullPath' resulted in a non-callable: {$type}");
            }

            $this->fixtures[$fullPath] = $result;
        }

        return $this->fixtures[$fullPath];
    }

    private function getContainer(ReflectionClass $class): ContainerInterface
    {
        try {
            $reflectedContainerMethod = $class->getMethod('getContainer');
        } catch (ReflectionException) {
            throw new RuntimeException("No 'getContainer' method on '$class->name'");
        }

        /** @var ContainerInterface|null $container */
        $container = $reflectedContainerMethod->invoke(null);
        if ($container === null) {
            throw new RuntimeException("Could not get container - is the kernel initialised?");
        }

        return $container;
    }
}
