<?php

namespace App\Commands;

use App\App;
use App\Interfaces\Commands\CommandInterface;
use App\Services\HtmlParseService;
use App\Services\PageService;
use Psr\Log\LoggerInterface;

class ScrapeCommand implements CommandInterface
{
    const PAGES = 1;
    
    const SELECTOR_QUESTION_LINK = ".qa-q-list-item .qa-q-item-title a";

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
        $postLinks = [];
    }

    /**
     * @param string[] $pagesHtml
     * @param string $selector
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
    }

    /**
     * @param $html
     * @param $selector
     * @return array
     */
    private function getPostLinksFromPage(string $html, string $selector) : array
    {
        $elements = $this->htmlParseService->getElements($html, $selector);
        $elements = $this->htmlParseService->asArray($elements);
        
        $links = [];
        foreach ($elements as $element) {
            $this->logger->info('element', ['element' => $element]);
        }
    }
}