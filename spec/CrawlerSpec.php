<?php

namespace spec\Nivv\Crawly;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;

class CrawlerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith('http://surkultur.se', 10);
        $this->shouldHaveType('Nivv\Crawly\Crawler');
    }

    public function it_throws_an_exception_if_the_url_is_invalid()
    {
        $url = 'x';
        $this->shouldThrow('Nivv\Crawly\Exceptions\InvalidUrl')->during('__construct', [$url, 2]);
    }

    public function it_can_return_a_link_array()
    {
        $this->beConstructedWith('http://surkultur.se', 10);
        $this->getLinks()->shouldBeArray();
    }
}
