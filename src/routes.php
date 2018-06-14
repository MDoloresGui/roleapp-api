<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->group('/api', function () use ($app) {
    $app->get('/users', 'getUsers');
    $app->get('/users/{name}', 'getUser');
    $app->get('/universes', 'getUniverses');
    $app->get('/universes/{name}', 'getUniverse');
    $app->post('/users', 'postUser');
    $app->post('/universes', 'postUniverse');
    $app->patch('/users/{name}', 'changePassword');
});
