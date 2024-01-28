<?php

declare(strict_types=1);

use App\Application\Actions\Template\RemoveTemplateAction;
use App\Application\Actions\Template\SaveTemplateAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use \App\Application\Actions\Template\ListTemplatesAction;
use \App\Application\Actions\Content\SaveContentAction;
use \App\Application\Actions\Template\ListTemplatesAllAction;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {



        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/content', function (Group $group) {
        $group->post('/save', SaveContentAction::class);
    });

    $app->group('/templates', function (Group $group) {
        $group->post('', ListTemplatesAction::class);
        $group->get('/all', ListTemplatesAllAction::class);
        $group->post('/save', SaveTemplateAction::class);
        $group->post('/remove', RemoveTemplateAction::class);
    });

    /*

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });*/
};
