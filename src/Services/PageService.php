<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

/**
 * Class PageService
 * @package App\Services
 */
class PageService
{
    const PER_PAGE = 20;
    const BASE_URL = 'https://godotengine.org';
    const ENDPOINT_QUESTIONS = '/qa/questions';

    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * @var Client
     */
    private $client;

    /**
     * PageService constructor.
     * @param LoggerInterface $logger
     * @param Client $client
     */
    public function __construct(LoggerInterface $logger, Client $client)
    {
        $this->logger = $logger;
        $this->client = $client;
    }

    /**
     * @param int $pages
     * @return array
     */
    public function getPages(int $pages = 1) : array
    {
        $pageData = [];
        for ($i = 1; $i <= $pages; $i++) {
            $pageData[] = $this->getPageHtml($i);
        }
        
        return $pageData;
    }

    /**
     * @param int $page
     * @return string
     */
    private function getPageHtml(int $page = 1) : string
    {
        $start = $this->pageToOffset($page);
        try {
            return $this
                ->client
                ->get(
                    self::ENDPOINT_QUESTIONS,
                    [
                        'query' => [
                            'start' => $start,
                        ]
                    ]
                )
                ->getBody();
        } catch (RequestException $e) {
            $this->logger->info('Could not get page html', [
                'url' => $e->getRequest()->getUri()->getPath(),
                'query' => $e->getRequest()->getUri()->getQuery(),
                'error' => (string)$e,
            ]);
            return '';
        }
        
    }

    /**
     * @param int $page
     * @return int
     */
    private function pageToOffset(int $page) : int
    {
        return ($page - 1) * self::PER_PAGE;
    }
}
