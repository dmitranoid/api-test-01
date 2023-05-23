<?php

declare(strict_types=1);

/*
use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
*/
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;


require __DIR__ . '/../vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..', $_ENV['APP_ENV'] ?? 'dev' . '.env');
$dotenv->load();

$containerBuilder = new ContainerBuilder();

$settings = require __DIR__ . '/../config/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/../config/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../config/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// middleware
$middleware = require __DIR__ . '/../config/middlewares.php';
$middleware($app);

// routes
$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$settings = $container->get('settings');

$displayErrorDetails = $settings['displayErrorDetails'];
$logError = $settings['logError'];
$logErrorDetails = $settings['logErrorDetails'];

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

/*
// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
*/
/*
// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);
*/
// Add Routing Middlewares
$app->addRoutingMiddleware();

// Add Body Parsing Middlewares
$app->addBodyParsingMiddleware();

// Add Error Middlewares
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
/*
$errorMiddleware->setDefaultErrorHandler($errorHandler);
*/
$app->run($request);

/*

$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
*/