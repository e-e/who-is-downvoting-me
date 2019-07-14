<?php

namespace App\Services;


use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractClientService
 * @package App\Services
 */
abstract class AbstractClientService
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var ClientInterface $client
     */
    protected $client;

    /**
     * AbstractClientService constructor.
     * @param LoggerInterface $logger
     * @param ClientInterface $client
     */
    public function __construct(LoggerInterface $logger, ClientInterface $client)
    {
        $this->logger = $logger;
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getClientBaseUrl() : string
    {
        return $this->client->getConfig()['base_uri'];
    }
}