<?php

declare(strict_types=1);

use App\ApiController\ItemApiResource;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    /*
        $app->options('/{routes:.*}', function (Request $request, Response $response) {
            // CORS Pre-Flight OPTIONS Request Handler
            return $response;
        });
    */
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('api test');
        return $response;
    });

    $app->get('/token', function (Request $request, $response) {
        $requested_scopes = $request->getParsedBody() ?: [];
        $now = new DateTime();
        $expiredAt = new DateTime(' + 60 minutes');
        $server = $request->getServerParams();
        $jti = (new \Tuupola\Base62())->encode(random_bytes(16));
        $payload = [
            'iat' => $now->getTimeStamp(),
            'exp' => $expiredAt->getTimeStamp(),
            'jti' => $jti,
            'sub' => $server['PHP_AUTH_USER']
        ];
        $secret = $_ENV['JWT_SECRET'];
        $token = \Firebase\JWT\JWT::encode($payload, $secret);
        $data['token'] = $token;
        $data['expires'] = $expiredAt->getTimeStamp();
        /** @var $response Slim\Psr7\Response */
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $response->withStatus(201)
            ->withHeader('Content-Type', 'application/json');

    });

    $app->group('/api', function (Group $group) {
        $group->any('/items', ItemApiResource::class);
        $group->any('/item/{id}', ItemApiResource::class);
    });
};