<?php

namespace spec\Nivv\Crawly;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CrawlerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith('surkultur.se');
        $this->shouldHaveType('Nivv\Crawly\Crawler');
    }

    public function it_can_traverse_an_url()
    {
        $this->beConstructedWith('surkultur.se');
        $url = null;
        $this->traverse($url);
        // $this->traverseSingle($url, 3)->shouldBeCalled();
        // $this->getGoutteClient()->shouldBeCalled();
    }

    public function it_can_return_link_array()
    {
    	$this->beConstructedWith('http://surkultur.se');
    	$this->traverse(null);
    	$this->getLinks();
    }
}
