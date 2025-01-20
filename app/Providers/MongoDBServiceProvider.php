<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MongoDB\Client;

class MongoDBServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $config = config('mongodb');

            $uri = sprintf(
                'mongodb://%s:%s',
                $config['host'],
                $config['port']
            );

            return new Client($uri);
        });
    }

    public function boot()
    {
        if ($this->app->environment() !== 'testing') {
            $client = $this->app->make(Client::class);
            $db = $client->selectDatabase(config('mongodb.database'));

            // Configurar índices para tweets
            $tweetsCollection = $db->selectCollection('tweets');
            foreach (config('mongodb.collections.tweets.indexes') as $index) {
                $tweetsCollection->createIndex($index['key']);
            }
        }
    }
}