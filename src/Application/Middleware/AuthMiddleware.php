<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $authorization = $request->getHeader("Authorization");
        if (empty($authorization) || strtoupper($authorization[0]) !== strtoupper("BEARER QlP5a94BIvqR2SLKUk_DIKCfv3OmobqQpWIn4muDzwY5DURxzT9vXivIY-XNKYkf8RfEgOdSad1S1izzC5tNzw")) {
            $response = new Response();
            $response->withStatus(403)
                ->getBody()
                ->write(json_encode(["statusCode" => 403, "data" => ["type" => "error", "message" => "You are not authenticated. Please, provide a valid API key."]]));
            return $response->withHeader("content-type", "application/json");
        }

        return $handler->handle($request);
    }
}
