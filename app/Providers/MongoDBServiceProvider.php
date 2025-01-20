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

            // Construimos una URI completa con credenciales y parámetros de autenticación
            $uri = sprintf(
                'mongodb://%s:%s@%s:%s/%s?authSource=admin',
                $config['username'],        // Usuario de MongoDB
                $config['password'],        // Contraseña
                $config['host'],            // Host (nombre del servicio en Docker)
                $config['port'],            // Puerto
                $config['database']         // Nombre de la base de datos
            );

            // Creamos el cliente con opciones adicionales de seguridad
            return new Client($uri, [
                'authSource' => 'admin',
                'retryWrites' => true,
                'w' => 'majority'
            ]);
        });
    }
}