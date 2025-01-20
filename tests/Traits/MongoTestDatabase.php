<?php

namespace Tests\Traits;

use MongoDB\Client;

trait MongoTestDatabase
{
    protected function getMongoClient(): Client
    {
        return new Client('mongodb://localhost:27017');
    }

    protected function cleanMongoCollection(string $collection): void
    {
        $this->getMongoClient()
            ->selectDatabase('uala-mgo')
            ->selectCollection($collection)
            ->deleteMany([]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanMongoCollection('tweets');
    }
}