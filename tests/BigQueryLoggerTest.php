<?php

/*
 * This file is part of the GooglBigQueryLogger package.
 * (c) Elixis Digital <support@elixis.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GoogleBigQueryLogger\Tests;

use GoogleBigQueryLogger\BigQueryLogger;
use PHPUnit\Framework\TestCase;

/**
 * Test unit for class BigQueryLogger.
 *
 * @author Anthony Papillaud <a.papillaud@elixis.com>
 *
 * @method testCredentialsFileIsNotGiven()
 *
 * @since 1.1.0
 * @version 1.1.0
 **/
class BigQueryLoggerTest extends TestCase
{
    /**
     * Test if method "..." retrun a BigQueryClient valide.
     * TODO make test
     *
     * @since 1.1.0
     * @version 1.1.0
     **/
    public function testBigQueryClient()
    {
    }

    /**
     * Throw an exception if credentials file is not given.
     * TODO make test
     *
     * @since 1.1.0
     * @version 1.1.0
     **/
    public function testCredentialsFileIsNotGiven()
    {
    }

    /**
     * Throw an exception if dataset name is not given.
     * TODO make test
     *
     * @since 1.1.0
     * @version 1.1.0
     **/
    public function testDatasetNameIsNotGiven()
    {
    }

    /**
     * Test if "SetExcludeEnv" method convert a string to array.
     *
     * @since 1.1.0
     * @version 1.1.0
     **/
    public function testSetExcludeEnvWithTwoEnv()
    {
        $bigQueryLogger = new BigQueryLogger();
        $excludeEnv = $bigQueryLogger->listExcludeEnv('[test, debug]');

        $this->assertIsArray($excludeEnv);
        $this->assertEquals(2, count($excludeEnv));
        $this->assertEquals('test', $excludeEnv[0]);
        $this->assertEquals('debug', $excludeEnv[1]);
    }

    /**
     * Test if "SetExcludeEnv" method convert a string to array.
     *
     * @since 1.1.0
     * @version 1.1.0
     **/
    public function testSetExcludeEnvWithOnlyOneEnv()
    {
        $bigQueryLogger = new BigQueryLogger();
        $excludeEnv = $bigQueryLogger->listExcludeEnv('[test]');

        $this->assertIsArray($excludeEnv);
        $this->assertEquals(1, count($excludeEnv));
        $this->assertEquals('test', $excludeEnv[0]);
    }

    /**
     * Test if "SetExcludeEnv" method convert a string to array
     * If string is empty return an empty array.
     *
     * @since 1.1.0
     * @version 1.1.0
     **/
    public function testSetExcludeEnvWithEmptyString()
    {
        $bigQueryLogger = new BigQueryLogger();
        $excludeEnv = $bigQueryLogger->listExcludeEnv('');

        $this->assertIsArray($excludeEnv);
        $this->assertEquals(0, count($excludeEnv));
    }

    /**
     * Method to acces private method.
     *
     * @param mixed $instance
     * @param string $property
     * @param mixed|null $arguments
     * @return mixed
     * @throws \ReflectionException
     * @version 1.1.0
     * @since 1.1.0
     */
    private function _invokeMethod($instance, string $property, $arguments = null)
    {
        $method = new \ReflectionMethod($instance, $property);
        $method->setAccessible(true);

        return ($arguments) ? $method->invokeArgs($instance, $arguments) : $method->invoke($instance);
    }
}
