<?php

namespace App\Services;

use DOMElement;
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

    /**
     * @param DOMElement $element
     * @param string $cssSelector
     * @return DOMNodeList
     */
    public function getElementsFromElement(DOMElement $element, string $cssSelector)
    {
        $xpath = new DOMXPath($element->ownerDocument);
        $xpathQuery = $this->cssConverter->toXPath($cssSelector);
        return $xpath->query($xpathQuery);
    }

    /**
     * @param DOMElement $element
     * @return string
     */
    public function getElementText(DOMElement $element) : string
    {
        return $element->textContent;
    }
    
    /**
     * @param DOMNodeList $list
     * @return array
     */
    public function asArray(DOMNodeList $list) : array
    {
        $array = [];

        for ($i = 0; $i < $list->length; $i++) {
            $array[] = $list->item($i);
        }
        
        return $array;
    }
}