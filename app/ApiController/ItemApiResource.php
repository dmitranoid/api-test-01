<?php

namespace App\ApiController;

use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ValidationException;
use App\Repositories\ItemsRepository;
use App\Services\ItemsService;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class ItemApiResource extends ApiResource
{

    public function index(): ResponseInterface
    {
        $items = $this->service->getAll();
        $data = array_reduce($items, function ($res, $item) { $res[] = $item->toArray(); return $res; }, [] );
        return $this->responseJson($data);
    }

    public function __construct(
        private ItemsService $service
    )
    {
    }

    public function get(): ResponseInterface
    {
        $id = $this->request->getAttribute('id');
        assert(!empty($id), 'id required');
        try {
            $item = $this->service->getById($id);
        } catch (RecordNotFoundException $e) {
            return $this->responseJson(['status' => 'not found'], 404);
        } catch (ValidationException $e) {
            return $this->responseJson(['status' => 'validation error'], 500);
        }
        return $this->responseJson($item->toArray());
    }

    public function delete(): ResponseInterface
    {
        $id = $this->request->getAttribute('id');
        assert(!empty($id), 'id required');
        $this->service->remove($id);
        return $this->responseJson(['status' => 'success']);
    }

    public function post(): ResponseInterface
    {
        try {
            $item = $this->service->create($this->request->getParsedBody());
        } catch (ValidationException $e) {
            return $this->responseJson(['status' => 'validation error'], 422);
        }
        return $this->responseJson($item->toArray());
    }

    public function put(): ResponseInterface
    {
        $id = $this->request->getAttribute('id');
        assert(!empty($id), 'id required');
        try {
            $item = $this->service->update([...$this->request->getParsedBody(), ...['id' => $id]]);
        } catch (RecordNotFoundException $e) {
            return $this->responseJson(['status' => 'not found'], 404);
        } catch (ValidationException $e) {
            return $this->responseJson(['status' => 'validation error'], 500);
        }
        return $this->responseJson($item->toArray());

    }

}