<?php

declare(strict_types=1);

return function (Slim\App $app) {
    $app->add(new \Tuupola\Middleware\JwtAuthentication([
        'path' => '/api',
        'algorithm' => 'HS256',
        'secret' => $_ENV['JWT_SECRET'],
        'attribute' => 'jwt',
        'error' => function ($response, $arguments) {
            $data['status'] = 401;
            $data['error'] = 'Unauthorized/'. $arguments['message'];
            return $response
                ->withHeader('Content-Type', 'application/json;charset=utf-8')
                ->getBody()->write(json_encode(
                    $data,
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                ));
        }
    ]));
};