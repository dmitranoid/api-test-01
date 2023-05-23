<?php

namespace App\ApiController;

use App\Exceptions\NotImplementedException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpNotImplementedException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

abstract class ApiResource
{
    protected RequestInterface $request;
    protected ResponseInterface $response;

    public function __invoke(
        RequestInterface  $request,
        ResponseInterface $response,
    )
    {
        $this->request = $request;
        $this->response = $response;

        switch ($request->getMethod()) {
            case 'GET' :
                if ($this->request->getAttribute('id')) {
                    return $this->get();
                }
                return $this->index();
            case 'POST' :
                // only root entity
                if (empty($this->request->getAttribute('id'))) {
                    return $this->post();
                }
                break;
            case 'PUT' :
                return $this->put();
            case 'DELETE' :
                return $this->delete();
            default:
                throw new HttpNotImplementedException($request);
        }
    }

    abstract public function index(): ResponseInterface;

    abstract public function get(): ResponseInterface;

    abstract public function post(): ResponseInterface;

    abstract public function delete(): ResponseInterface;

    abstract public function put(): ResponseInterface;

    protected function responseJson($data, $statusCode = 200):ResponseInterface {
        $this->response->getBody()->write(json_encode($data,  JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}
