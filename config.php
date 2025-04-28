<?php

return [
    'bot_token' => getenv('TELEGRAM_BOT_TOKEN'),
    'db' => [
        'host' => getenv('MYSQLHOST') ?: 'localhost',
        'dbname' => getenv('MYSQL_DATABASE') ?: 'railway',
        'user' => getenv('MYSQLUSER') ?: 'root',
        'password' => getenv('MYSQLPASSWORD') ?: '',
    ],
];
