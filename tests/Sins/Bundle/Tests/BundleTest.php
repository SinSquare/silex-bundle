<?php

namespace SinSquare\Bundle\Tests;

use Silex\Application;
use SinSquare\Bundle\Tests\BundleTest\Controller\TestController;
use SinSquare\Bundle\Tests\BundleTest\TestBundle;

class BundleTest extends \PHPUnit_Framework_TestCase
{
    protected $application;
    protected $controller;
    protected $metaArray;
    protected $dataArray;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->register(new TestBundle());
    }

    protected function tearDown()
    {
        unset($this->application);
    }

    public function testControllerIds()
    {
        $ids = $this->application['controller.ids'];

        $this->assertContains('TestController', $ids);
        $this->assertNotContains('BadController', $ids);
    }

    public function testControllerLoading()
    {
        $this->assertArrayHasKey('TestController', $this->application);
        $this->assertInstanceOf(TestController::class, $this->application['TestController']);
        $this->assertEquals('function1', $this->application['TestController']->function1());
    }
}
