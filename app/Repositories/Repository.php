<?php

declare(strict_types=1);

namespace App\Repositories;
namespace App\Repositories;

interface Repository
{
    public function getAll(?array $filter, ?array $order, ?int $limit, ?int $offset):array;

    public function getById($id):array;

    /**
     * @param array $data
     * @return mixed  inserted id
     */
    public function create(array $data):mixed;

    public function update(array $data);

    public function delete($id);
}