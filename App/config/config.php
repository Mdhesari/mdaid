<?php

/**
 * 
 * App Main Config
 * 
 */

return [

    /**
     * Database Connections
     */
    'connection' => [
        'mysql' => [
            'user' => DB_USER,
            'password' => DB_PASSWORD,
            'database' => DB_NAME,
            'host' => DB_HOST,
            'charset' => DB_CHARSET
        ]
    ],

    /**
     * DriverBot Data
     */
    'bot' => [

        /**
         * Telegram Bot 
         */
        'telegram' => [
            'token' => BOT_TOKEN,
            'username' => BOT_USERNAME,
            'async' => BOT_ASYNC,
        ]

    ]

];
