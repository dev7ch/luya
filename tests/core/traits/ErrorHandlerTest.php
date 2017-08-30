<?php

namespace luyatests\core\traits;

use Exception;
use luya\console\ErrorHandler;
use luyatests\LuyaWebTestCase;

class ErrorHandlerTest extends LuyaWebTestCase
{
    public function testExceptionTrace()
    {
        try {
            $stud = new ErrorHandler();
            $response = $stud->getExceptionArray(new Exception('foobar'));

            $this->assertArrayHasKey('message', $response);
            $this->assertArrayHasKey('file', $response);
            $this->assertArrayHasKey('line', $response);
            $this->assertArrayHasKey('requestUri', $response);
            $this->assertArrayHasKey('serverName', $response);
            $this->assertArrayHasKey('date', $response);
            $this->assertArrayHasKey('trace', $response);
            $this->assertArrayHasKey('ip', $response);
            $this->assertArrayHasKey('get', $response);
            $this->assertArrayHasKey('post', $response);
            $this->assertTrue(is_array($response['trace']));
        } catch (Exception $e) {
            $this->assertEquals('Error: foobar', $e->getMessage());
        }
    }

    public function testExceptionStringTrace()
    {
        try {
            $stud = new ErrorHandler();
            $response = $stud->getExceptionArray('Is a string exception');

            $this->assertArrayHasKey('message', $response);
            $this->assertArrayHasKey('file', $response);
            $this->assertArrayHasKey('line', $response);
            $this->assertArrayHasKey('requestUri', $response);
            $this->assertArrayHasKey('serverName', $response);
            $this->assertArrayHasKey('date', $response);
            $this->assertArrayHasKey('trace', $response);
            $this->assertArrayHasKey('ip', $response);
            $this->assertArrayHasKey('get', $response);
            $this->assertArrayHasKey('post', $response);
            $this->assertTrue(is_array($response['trace']));
        } catch (Exception $e) {
            $this->assertEquals('Error: foobar', $e->getMessage());
        }
    }

    public function testExceptionArrayTrace()
    {
        try {
            $stud = new ErrorHandler();
            $response = $stud->getExceptionArray(['array', 'is', 'exception']);

            $this->assertArrayHasKey('message', $response);
            $this->assertArrayHasKey('file', $response);
            $this->assertArrayHasKey('line', $response);
            $this->assertArrayHasKey('requestUri', $response);
            $this->assertArrayHasKey('serverName', $response);
            $this->assertArrayHasKey('date', $response);
            $this->assertArrayHasKey('trace', $response);
            $this->assertArrayHasKey('ip', $response);
            $this->assertArrayHasKey('get', $response);
            $this->assertArrayHasKey('post', $response);
            $this->assertTrue(is_array($response['trace']));
        } catch (Exception $e) {
            $this->assertEquals('Error: foobar', $e->getMessage());
        }
    }
}
