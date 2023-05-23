<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\AppException;
use App\Exceptions\RecordNotFoundException;
use PDO;
use PDOException;

abstract class BaseRepository implements Repository
{
    protected string $tableName = '';
    protected string $keyField = 'id';
    protected PDO $pdo;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @throws AppException
     */
    protected function getLastInsertedId(): string
    {
        try {
            return $this->pdo->lastInsertId($this->keyField);
        } catch (PDOException $e) {
            throw new AppException('database error', 0, $e);
        }
    }

    /**
     * @throws AppException
     */
    public function getAll(?array $filter, ?array $order, ?int $limit, ?int $offset): array
    {
        assert(!empty($this->tableName), 'table name is empty');
        try {
            $query = "select * from $this->tableName";
            $sth = $this->pdo->query($query);
            return $sth->fetchAll();
        } catch (PDOException $e) {
            throw new AppException('database error', 0, $e);
        }
    }

    /**
     * @param $id
     * @return array
     * @throws AppException
     * @throws RecordNotFoundException
     */
    public function getById($id): array
    {
        assert(!empty($this->tableName), 'table name is empty');
        try {
            // can be done via
            // return getAll([id => $id]]);
            $query = "select * from $this->tableName where $this->keyField = :id";
            $sth = $this->pdo->prepare($query);
            $sth->execute(['id' => $id]);
            $data = $sth->fetch();
            if ($data === false) throw new RecordNotFoundException("record with $this->keyField = $id not found in $this->tableName");
            return $data;
        } catch (PDOException $e) {
            throw new AppException('database error', 0, $e);
        }
    }

    /**
     * @throws AppException
     */
    public function create(array $data): mixed
    {
        assert(!empty($this->tableName), 'table name is empty');
        $prep = [];
        unset($data[$this->keyField]);
        foreach ($data as $k => $v) {
            $prep[':' . $k] = $v;
        }
        $query = "INSERT INTO $this->tableName (" . implode(', ', array_keys($data)) . ") VALUES (" . implode(', ', array_keys($prep)) . ")";
        try {
            $sth = $this->pdo->prepare($query);
            $sth->execute($prep);
        } catch (PDOException $e) {
            throw new AppException('database error', 0, $e);
        }
        return $this->getLastInsertedId();
    }

    /**
     * @throws AppException
     */
    public function update(array $data)
    {
        assert(!empty($this->tableName), 'table name is empty');
        try {
            $prep = [];
            $updateFields = [];
            foreach ($data as $k => $v) {
                $prep[':' . $k] = $v;
                if ($k !== $this->keyField) {
                    $updateFields[$k] = "$k = :$k";
                }
            }
            $query = "update $this->tableName set  " . implode(', ', array_values($updateFields)) . " where $this->keyField = :$this->keyField";
            $sth = $this->pdo->prepare($query);
            $sth->execute($prep);
            // todo
            if ($sth->rowCount() !== 1) {
            }
        } catch (PDOException $e) {
            throw new AppException('database error', 0, $e);
        }
    }

    /**
     * @throws AppException
     */
    public function delete($id)
    {
        assert(!empty($this->tableName), 'table name is empty');
        try {
            $query = "delete from $this->tableName where $this->keyField = :id ";
            $sth = $this->pdo->prepare($query);
            $sth->execute(['id' => $id]);
            // todo
            if ($sth->rowCount() !== 1) {
            }
        } catch (PDOException $e) {
            throw new AppException('database error', 0, $e);
        }
    }
}