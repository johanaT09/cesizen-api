<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sentry\ClientBuilder;
use Sentry\State\HubInterface;
use Sentry\Transport\HttpTransport;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\StreamHandler;

class SentryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(HubInterface::class, function ($app) {
            $userConfig = $app['config']->get('sentry', []);
            
            // Forcer Guzzle à utiliser les Streams PHP natifs (Bypass cURL)
            $streamHandler = new StreamHandler();
            $guzzleClient = new Client([
                'handler' => $streamHandler,
                'verify' => false,
                'timeout' => 5,
            ]);

            $builder = ClientBuilder::create($userConfig);
            $builder->setHttpClient($guzzleClient);

            $client = $builder->getClient();
            $hub = $app->make(HubInterface::class);
            $hub->bindClient($client);

            return $hub;
        });
    }
}