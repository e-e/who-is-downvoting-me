<?php

$app->provider(\App\Providers\PageServiceProvider::class);
$app->provider(\App\Providers\LoggerProvider::class);

$app->register(\GuzzleHttp\ClientInterface::class, \GuzzleHttp\Client::class);