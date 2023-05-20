<?php

namespace DFe\Logger;

class LogTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        Log::getInstance()->setHandler(new \Monolog\Handler\NullHandler());
    }

    protected function tearDown(): void
    {
        Log::getInstance()->setHandler(null);
    }

    public function testLogs()
    {
        $this->assertNotNull(Log::getInstance());
        Log::getInstance()->fromArray(Log::getInstance());
        Log::getInstance()->fromArray(Log::getInstance()->toArray());
        Log::getInstance()->fromArray(null);
        Log::error('Error Test');
        Log::warning('Warning Test');
        Log::debug('Debug Test');
        Log::info('Information Test');
    }
}
