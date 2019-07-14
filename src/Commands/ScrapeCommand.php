<?php

namespace App\Commands;

use App\App;
use App\Interfaces\Commands\CommandInterface;
use App\Services\HtmlParseService;
use App\Services\PageService;
use DOMElement;
use Psr\Log\LoggerInterface;

/**
 * Class ScrapeCommand
 * @package App\Commands
 */
class ScrapeCommand implements CommandInterface
{
    const PAGES = 10;
    
    const SELECTOR_QUESTION_LINK = '.qa-q-list-item .qa-q-item-title a';
    const SELECTOR_COMMENT = '.qa-c-list-item.comment';
    const SELECTOR_COMMENT_USER = ".qa-user-link.url.fn.entry-title nickname";
    const SELECTOR_COMMENT_TEXT = ".entry-content";
    
    const TARGET_NAME = 'Eric Ellingson';
    
    /**
     * @var LoggerInterface $logger
     */
    private $logger;
    
    /**
     * @var PageService
     */
    private $pageService;

    /**
     * @var HtmlParseService $htmlParseService
     */
    private $htmlParseService;

    /**
     * ScrapeCommand constructor.
     * @param LoggerInterface $logger
     * @param PageService $pageService
     * @param HtmlParseService $htmlParseService
     */
    public function __construct(
        LoggerInterface $logger,
        PageService $pageService,
        HtmlParseService $htmlParseService
    ) {
        $this->logger = $logger;
        $this->pageService = $pageService;
        $this->htmlParseService = $htmlParseService;
    }

    /**
     * @param App $app
     */
    public function run(App $app)
    {
        $listPagesHtml = $this->pageService->getPages(self::PAGES);
        $postLinks = $this->getPostLinksFromPages($listPagesHtml);
        
        foreach ($postLinks as $postUrl) {
            $postHtml = $this->pageService->getHtml($postUrl);
            $comments = $this->htmlParseService->getElements($postHtml, self::SELECTOR_COMMENT);
            $comments = $this->htmlParseService->asArray($comments);
            $comments = array_filter($comments, [$this, "filterComments"]);
            $this->logger->debug('COMMENTS', [
                'comments' => $comments,
            ]);
        }
    }

    /**
     * @param DOMElement $item
     * @return bool
     */
    private function filterComments(DOMElement $element) : bool
    {
        $name = $this->htmlParseService->getElementsFromElement($element, self::SELECTOR_COMMENT_USER);
        $name = $this->htmlParseService->asArray($name);
        
        if (count($name) !== 1) {
            $this->logger->notice("Error retrieving name from comment");
            return false;
        }
        
        $name = $this->htmlParseService->getElementText($name[0]);
        
        return $name === self::TARGET_NAME;
    }
    
    /**
     * @param string[] $pagesHtml
     * @return array
     */
    private function getPostLinksFromPages(array $pagesHtml) : array
    {
        $allLinks = [];
        
        foreach ($pagesHtml as $html) {
            $links = $this->getPostLinksFromPage($html, self::SELECTOR_QUESTION_LINK);
            foreach ($links as $link) {
                $allLinks[] = $link;
            }
        }
        
        return $allLinks;
    }

    /**
     * @param $html
     * @param $selector
     * @return array
     */
    private function getPostLinksFromPage(string $html, string $selector) : array
    {
        $elements = $this->htmlParseService->getElements($html, $selector);
        print_r($elements);
        $elements = $this->htmlParseService->asArray($elements);
        
        $baseUrl = $this->pageService->getClientBaseUrl();
        
        $links = [];
        foreach ($elements as $element) {
            /** @var DOMElement $element */
            $href = $element->getAttribute("href");
            $href = preg_replace("/^.\//", "", $href);
            $url = "$baseUrl/$href";
            $this->logger->info('element', ['url' => $url]);
            $links[] = $url;
        }
        
        return $links;
    }
}