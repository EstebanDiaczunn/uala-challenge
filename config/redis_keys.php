<?php

/**
 * Configuración de las claves de Redis
 *
 * Este archivo contiene todas las claves utilizadas en Redis para el almacenamiento
 * en caché de la aplicación. Las claves están organizadas por contexto/dominio
 * para facilitar su mantenimiento y evitar colisiones.
 *
 * El formato %s se utiliza como placeholder para valores dinámicos (ej: IDs)
 */

return [
    'user' => [
        'followers_count' => 'u:%s:followers_count',
        'following_count' => 'u:%s:following_count',
    ],
];
