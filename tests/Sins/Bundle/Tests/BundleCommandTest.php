<?php

namespace SinSquare\Bundle\Tests;

use Knp\Console\Application as ConsoleApplication;
use Knp\Provider\ConsoleServiceProvider;
use Silex\Application;
use SinSquare\Bundle\Tests\BundleTest\TestBundle;

class BundleCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $application;
    protected $controller;
    protected $metaArray;
    protected $dataArray;

    protected function setUp()
    {
        $this->application = new Application();

        $this->application->register(
            new ConsoleServiceProvider(),
            array(
                'console.project_directory' => __DIR__,
                'console.name' => 'TestConsole',
                'console.version' => '12.123',
            )
        );
        $this->application->register(new TestBundle());
    }

    protected function tearDown()
    {
        unset($this->application);
    }

    public function testConsole()
    {
        $console = $this->application['console'];

        $this->assertInstanceOf(ConsoleApplication::class, $console);

        $this->assertSame('TestConsole', $console->getName());
        $this->assertSame('12.123', $console->getVersion());
        $this->assertSame(__DIR__, $console->getProjectDirectory());
    }

    public function testCommand()
    {
        $console = $this->application['console'];
        $this->assertTrue($console->has('test:command'));
    }
}
