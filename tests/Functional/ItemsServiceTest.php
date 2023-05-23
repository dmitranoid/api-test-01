<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Models\ItemModel;
use App\Repositories\ItemsRepository;
use App\Services\ItemsService;
use PDO;
use PHPUnit\TextUI\Help;
use Tests\Utils\Helpers;

class ItemsServiceTest extends \PHPUnit\Framework\TestCase
{
    private ItemsRepository $itemsRepository;
    private PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        Helpers::loadTestEnv();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = Helpers::getPdo();
        Helpers::initDb($this->pdo, __DIR__ . '/../db-init.sql');

        $this->itemsRepository = new ItemsRepository($this->pdo);
    }

    public function testGetAllStubbed()
    {
        $items = [
            [
                "id" => '1',
                "name" => "name1",
                "phone" => "phone1",
                "key" => "key1",
                "created_at" => "2023-05-21T23:24:09+03:00",
                "updated_at" => "2023-05-22T00:20:16+03:00"
            ],
            [
                "id" => '2',
                "name" => "name2",
                "phone" => "phone2",
                "key" => "key2",
                "created_at" => "2023-05-21T23:24:09+03:00",
                "updated_at" => "2023-05-22T00:20:16+03:00"
            ],
        ];

        $rep = $this->createStub(ItemsRepository::class);
        $rep->method('getAll')
            ->willReturn($items);
        $service = new ItemsService($rep);

        $expected = array_reduce($items, function ($result, $item) {
            $result[] = ItemModel::fromArray($item);
            return $result;
        }, []);

        $this->assertEquals($expected, $service->getAll());
    }

    public function testGetByIdStubbed()
    {
        $item =
            [
                "id" => '1',
                "name" => "name1",
                "phone" => "phone1",
                "key" => "key1",
                "created_at" => "2023-05-21T23:24:09+03:00",
                "updated_at" => "2023-05-22T00:20:16+03:00"
            ];

        $rep = $this->createStub(ItemsRepository::class);
        $rep->method('getById')
            ->willReturn($item);
        $service = new ItemsService($rep);

        $this->assertEquals(ItemModel::fromArray($item), $service->getById(1));
    }


    public function testGetByIdDb()
    {
        $sth = $this->pdo->query('select * from items where id=:id');
        $sth->execute(['id' => 1]);
        $dbItem = $sth->fetch();
        $service = new ItemsService($this->itemsRepository);

        $this->assertEquals(ItemModel::fromArray($dbItem), $service->getById(1));
    }

    public function testCreate()
    {
        $data = [
            "name" => "name4",
            "phone" => "phone4",
            "key" => "key4",
        ];
        $service = new ItemsService($this->itemsRepository);
        $item = $service->create($data);

        $sth = $this->pdo->query('select * from items where id=:id');
        $sth->execute(['id' => $item->id]);
        $dbItem = $sth->fetch();

        $this->assertEquals($dbItem, $item->toArray());

    }

    public function testRemove()
    {
        $service = new ItemsService($this->itemsRepository);
        $sth = $this->pdo->query('select id from items limit 1');
        $id = $sth->fetch(PDO::FETCH_COLUMN);

        $service->remove($id);

        $sth = $this->pdo->prepare('select * from items where id=:id');
        $sth->execute(['id' => $id]);

        $data = $sth->fetchAll();

        $this->assertEquals(0, count($data));

    }

    public function testUpdate()
    {
        $service = new ItemsService($this->itemsRepository);
        $sth = $this->pdo->query('select id from items limit 1');
        $id = $sth->fetch(PDO::FETCH_COLUMN);

        $item = $service->getById($id);

        $item = $service->update([
            'id' => $id,
            'name' => $item->name . '-upd',
        ]);

        $sth = $this->pdo->prepare('select * from items where id=:id');
        $sth->execute(['id' => $id]);

        $itemDb = $sth->fetch();

        $this->assertEquals($item->name, $item->toArray()['name']);

    }


}