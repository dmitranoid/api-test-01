<?php

declare(strict_types=1);

return function (\DI\ContainerBuilder $c) {
    $c->addDefinitions([
        'settings' => [
            'displayErrorDetails' => (bool)$_ENV['DISPLAY_ERROR_DETAILS'],
            'logError' => (bool)$_ENV['LOG_ERROR'] ?? false,
            'logErrorDetails' => (bool)$_ENV['LOG_ERROR_DETAILS'] ?? false,
            'db' => [
                'driver' => $_ENV['DB_DRIVER'],
                'database' => $_ENV['DB_NAME'],
                'username' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD'],
            ]
        ]]);
};