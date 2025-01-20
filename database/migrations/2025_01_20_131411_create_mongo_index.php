<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Client;

return new class extends Migration {
    public function up(): void
    {
        $uri = sprintf(
            'mongodb://%s:%s@%s:%s/%s?authSource=admin',
            config('mongodb.username'),        // usuario
            config('mongodb.password'),        // contraseña
            config('mongodb.host'),            // host
            config('mongodb.port'),            // puerto
            config('mongodb.database')         // base de datos
        );

        // Creamos el cliente de MongoDB con opciones adicionales de autenticación
        $client = new Client($uri, [
            'authSource' => 'admin',
            // Estas opciones ayudan a manejar problemas de conexión
            'serverSelectionTimeoutMS' => 5000,
            'connectTimeoutMS' => 10000,
        ]);

        // Seleccionamos la base de datos
        $db = $client->selectDatabase(config('mongodb.database'));

        try {
            // Obtenemos la colección y creamos los índices
            $tweetsCollection = $db->selectCollection('tweets');

            // Creamos los índices necesarios para optimizar las consultas
            $tweetsCollection->createIndex(
                ['user_id' => 1, 'created_at' => -1],
                [
                    'background' => true,
                    'name' => 'user_tweets_timestamp',
                    'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY),
                ]
            );

            // Índice para búsquedas por fecha
            $tweetsCollection->createIndex(
                ['created_at' => -1],
                [
                    'background' => true,
                    'name' => 'tweets_timestamp',
                    'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY),
                ]
            );

        } catch (\Exception $e) {
            \Log::error("Error creating MongoDB indexes: " . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function down(): void
    {
        // La conexión con autenticación también es necesaria para eliminar índices
        $uri = sprintf(
            'mongodb://%s:%s@%s:%s/%s?authSource=admin',
            config('mongodb.username'),
            config('mongodb.password'),
            config('mongodb.host'),
            config('mongodb.port'),
            config('mongodb.database')
        );

        $client = new Client($uri, ['authSource' => 'admin']);

        try {
            $db = $client->selectDatabase(config('mongodb.database'));
            $tweetsCollection = $db->selectCollection('tweets');

            // Eliminamos los índices por nombre
            $tweetsCollection->dropIndex('user_tweets_timestamp');
            $tweetsCollection->dropIndex('tweets_timestamp');

        } catch (\Exception $e) {
            \Log::error("Error dropping MongoDB indexes: " . $e->getMessage());
            throw $e;
        }
    }
};