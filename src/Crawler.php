<?php

namespace Nivv\Crawly;

use Nivv\Crawly\Parser;
use Goutte\Client as GoutteClient;
use Nivv\Crawly\Exceptions\InvalidUrl;
use Guzzle\Http\Exception\CurlException;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Crawler
{
    /**
     * The base URL from which the crawler begins crawling
     * @var string
     */
    protected $baseUrl;

    /**
     * The max depth the crawler will crawl
     * @var int
     */
    protected $maxDepth;

    /**
     * Array of links (and related data) found by the crawler
     * @var array
     */
    protected $links;

    /**
     * Array of links that has been visited
     * @var array
     */
    protected $visited;

    /**
     * Array of links that returned an error
     * @var array
     */
    protected $faultyLinks;

    /**
     * Constructor
     * @param string $baseUrl
     * @param int    $maxDepth
     */
    public function __construct($baseUrl, $maxDepth = 3)
    {
        $this->parser = new Parser;
        $this->baseUrl = $baseUrl;
        $this->maxDepth = $maxDepth;
        $this->links = [];

        if (! filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidUrl;
        }
    }

    /**
     * Initiate the crawl
     * @param string $url
     */
    public function traverse($url = null)
    {
        if ($url === null) {
            $url = $this->baseUrl;
        }

        $this->traverseSingle($url, $this->maxDepth);
    }

    /**
     * Get links (and related data) found by the crawler
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }


    /**
     * Crawl single URL
     * @param string $url
     * @param int    $depth
     */
    protected function traverseSingle($url, $depth)
    {
        //Check if the url is in the visited array
        if (isset($this->visited[$url])) {
            return;
        }

        // Check if the url is valid
        if (! $this->parser->validUrl($url, $this->baseUrl)) {
            $this->faultyLinks[$url];
            return;
        }

        try {
            $client = $this->getGoutteClient();
            $crawler = $client->request('GET', $url);
            $statusCode = $client->getResponse()->getStatus();
            $contentType = $client->getResponse()->getHeader('Content-Type');
            $this->visited[$url] = $url;


            if ($statusCode === 200) {
                if (strpos($contentType, 'text/html') !== false || strpos($contentType, 'application/pdf') !== false) {
                    $this->links[$url]['path'] = $url;
                    $this->links[$url]['content_type'] = $contentType;
                    $this->links[$url]['depth'] = $depth;


                    $childLinks = [];
                    $childLinks = $this->extractLinksInfo($crawler, $url);
                    $this->traverseChildren($childLinks, $depth - 1);
                }
            }
        } catch (CurlException $e) {
            $this->faultyLinks[$url]['status_code'] = '404';
            $this->faultyLinks[$url]['error_code'] = $e->getCode();
            $this->faultyLinks[$url]['error_message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->faultyLinks[$url]['status_code'] = '404';
            $this->faultyLinks[$url]['error_code'] = $e->getCode();
            $this->faultyLinks[$url]['error_message'] = $e->getMessage();
        }
    }


    /**
     * create and configure goutte client used for scraping
     * @return GoutteClient
     */
    protected function getGoutteClient()
    {
        $client = new GoutteClient();
        $client->followRedirects();
        $guzzleClient = new \GuzzleHttp\Client(array(
            'curl' => array(
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ),
        ));

        $client->setClient($guzzleClient);
        return $client;
    }

    protected function extractLinksInfo($crawler, $url)
    {
        $links = $crawler->filter('a')->each(function (DomCrawler $node, $i) {
            //Do not bring in links that has been visited
            if (! isset($this->visited[$node->link()->getUri()])) {
                return $node->link()->getUri();
            }
        });
        return $links;
    }

    protected function traverseChildren($childLinks, $depth)
    {
        if ($depth === 0) {
            return;
        }
        foreach ($childLinks as $url) {
            echo ' ' .$depth. ' ';
            $this->traverseSingle($this->parser->normalize($url), $depth);
        }
    }
}
