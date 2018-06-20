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
    $app->get('/universes/byname/{name}', 'getUniverseByName');
    $app->get('/universes/{id}', 'getUniverseById');
    $app->get('/characters', 'getCharacters');
    $app->get('/characters/{id}', 'getCharacter');
    $app->get('/users/{id}/characters', 'getCharactersByUser');
    $app->get('/users/{id}/rolelines', 'getRoleLinesByUser');
    $app->get('/rolelines/byregexp/{regex}', 'getRoleLinesByRegex');
    $app->get('/rolelines/byregexp/', 'getRoleLines');
    $app->get('/rolelines/{id}/participants', 'getCharRoleLineByRoleLine');
    $app->get('/rolelines/{id}/posts', 'getPostsByRoleLine');
    $app->post('/users', 'postUser');
    $app->post('/rolechars', 'postCharRoleLine');
    $app->post('/characters', 'postCharacter');
    $app->post('/universes', 'postUniverse');
    $app->post('/rolelines', 'postRoleLine');
    $app->post('/posts', 'postPost');
    
    $app->patch('/users/{name}', 'changePassword');
});
