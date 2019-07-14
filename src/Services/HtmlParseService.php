<?php

namespace App\Services;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use \Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Class HtmlParseService
 * @package App\Services
 */
class HtmlParseService
{
    /**
     * @var CssSelectorConverter $cssConverter
     */
    private $cssConverter;

    /**
     * HtmlParseService constructor.
     * @param CssSelectorConverter $cssConverter
     */
    public function __construct(CssSelectorConverter $cssConverter)
    {
        $this->cssConverter = $cssConverter;
    }

    /**
     * @param string $html
     * @param string $cssSelector
     * @return DOMNodeList
     */
    public function getElements(string $html, string $cssSelector) : DOMNodeList
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        $xpath = new DOMXPath($doc);
        $xpathQuery = $this->cssConverter->toXPath($cssSelector);
        
        $elements = $xpath->query($xpathQuery);
        
        return $elements;
    }
    
    public function asArray(DOMNodeList $list) : array
    {
        $array = [];

        for ($i = 0; $i < $list->count(); $i++) {
            $array[] = $list->item($i);
        }
        
        return $array;
    }
}