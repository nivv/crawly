<?php

namespace Nivv\Crawly;

class Parser
{
    public function normalize($url)
    {
        return preg_replace('@#.*$@', '', $url);
    }

    public function validUrl($url, $baseUrl)
    {
        $linkParts = parse_url($url);
        $originalLinkParts = parse_url($baseUrl);
        if (empty($linkParts['host']) || $linkParts['host'] !== $originalLinkParts['host'] || isset($linkParts['fragment'])) {
            return false;
        }
        return true;
    }
}
