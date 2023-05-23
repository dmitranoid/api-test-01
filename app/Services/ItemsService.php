<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AppException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ValidationException;
use App\Models\ItemModel;
use App\Repositories\ItemsRepository;

final class ItemsService
{
    public function __construct(
        private ItemsRepository $repository,
    )
    {
    }

    public function getAll(array $filter = [], array $order = [], int $limit = 0, int $offset = 0): array
    {
        $dbData = $this->repository->getAll($filter, $order, $limit, $offset);
        $data = [];
        foreach ($dbData as $dbItem) {
            $data[] = ItemModel::fromArray($dbItem);
        }
        return $data;
    }

    /**
     * @throws AppException
     * @throws ValidationException
     */
    public function getById($id): ItemModel
    {
        return ItemModel::fromArray($this->repository->getById($id));
    }

    /**
     * @throws AppException
     * @throws ValidationException
     */
    public function create(array $data): ItemModel
    {
        unset($data['id']);
        $item = ItemModel::fromArray($data);
        $item->created_at = new \DateTime();
        $item->updated_at = new \DateTime();
        $id = $this->repository->create($item->toArray());
        $item->id = (int)$id;
        return $item;
    }

    public function remove(int $id)
    {
        $this->repository->delete($id);
    }

    /**
     * @throws AppException
     * @throws RecordNotFoundException
     * @throws ValidationException
     */
    public function update(array $data): ItemModel
    {
        $dbItem = $this->repository->getById($data['id']);

        $item = ItemModel::fromArray([...$dbItem, ...$data]);
        $item->updated_at = new \DateTime();
        $this->repository->update($item->toArray());
        return $item;
    }

}