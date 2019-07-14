<?php

namespace App\Providers;

use App\App;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerProvider extends Provider
{
    public function provide(App $app)
    {
        $app->register(LoggerInterface::class, function(App $app) {
            $log = new Logger('name');
            $streamHandler = new StreamHandler(
                $app->getBaseDir() . '/storage/logs/app.log', 
                Logger::DEBUG
            );
            $log->pushHandler($streamHandler);
            
            return $log;
        });
    }
}