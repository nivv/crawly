<?php

namespace spec\Nivv\Crawly;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;

class ParserSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Nivv\Crawly\Parser');
    }

    public function it_normalizes_an_url()
    {
        $url = 'http://bad-hash.com/#';
        $url2 = 'http://bad-hash.com/produkter#';
        $this->normalize($url)->shouldReturn('http://bad-hash.com/');
        $this->normalize($url2)->shouldReturn('http://bad-hash.com/produkter');
    }

    public function it_validates_an_url()
    {
        $url = 'http://stuff.com/produkter#page-1';
        $baseUrl = 'http://stuff.com';
        $this->validUrl($url, $baseUrl)->shouldReturn(false);

        $url = 'http://other-domain.com/produkter';
        $baseUrl = 'http://stuff.com';
        $this->validUrl($url, $baseUrl)->shouldReturn(false);

        $url = 'http://stuff.com/produkter';
        $baseUrl = 'http://stuff.com';
        $this->validUrl($url, $baseUrl)->shouldReturn(true);
    }
}
