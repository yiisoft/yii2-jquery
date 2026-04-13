<?php

declare(strict_types=1);

namespace yii\jquery\tests;

use ReflectionObject;
use Yii;
use yii\jquery\tests\support\ApplicationFactory;

/**
 * Base test case for jquery.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 1.0
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $logger = Yii::getLogger();
        $logger->flush();
    }

    /**
     * Asserting two strings equality ignoring line endings.
     *
     * @param string $expected Expected string.
     * @param string $actual Actual string.
     * @param string $message Failure message.
     */
    protected function assertEqualsWithoutLE(string $expected, string $actual, string $message = ''): void
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        self::assertEquals($expected, $actual, $message);
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication(): void
    {
        ApplicationFactory::destroy();
    }

    /**
     * Invokes an inaccessible method. Only for protected guards that cannot be reached through public API.
     *
     * @param object $object Object to invoke method on.
     * @param string $method Method name.
     * @param array $args Method arguments.
     */
    protected function invokeMethod(object $object, string $method, array $args = []): mixed
    {
        $reflection = new ReflectionObject($object);

        return $reflection->getMethod($method)->invokeArgs($object, $args);
    }

    /**
     * Populates Yii::$app with a new web application with jQuery enabled.
     *
     * @param array<string, mixed> $config Application configuration to merge with the default configuration.
     * See {@see ApplicationFactory::web()} for details.
     */
    protected function mockWebApplication(array $config = []): void
    {
        ApplicationFactory::web($config);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockWebApplication();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->destroyApplication();
    }
}
