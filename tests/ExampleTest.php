<?php

namespace Nivv\Crawly\Tests;

use Nivv\Crawly\Crawler;

class ExampleTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function it_is_constructable()
    {
    	$crawler = new Crawler('http://httpbin.org', 10);
        $this->assertInstanceOf('Nivv\Crawly\Crawler', $crawler);
    }
}
