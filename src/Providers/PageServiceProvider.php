<?php

namespace App\Providers;

use App\App;
use App\Services\PageService;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class PageServiceProvider extends Provider
{
    public function provide(App $app) {
        $app->register(PageService::class, function(App $app) {
            $logger = $app->gimme(LoggerInterface::class);
            $client = new Client([
                'base_uri' => 'https://godotengine.org'
            ]);
            
            return new PageService($logger, $client);
        });
    }
}