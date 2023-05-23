<?php

declare(strict_types=1);

namespace Tests\Acceptance;


use GuzzleHttp\Client;
use PHPUnit\TextUI\Help;
use Tests\Utils\Helpers;

class ItemsAcceptanceTest extends \PHPUnit\Framework\TestCase
{
    private Client $http;
    private \PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass(); // TODO: Change the autogenerated stub
        Helpers::loadTestEnv('dev.env');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->http = new Client([
            'base_uri' => $_ENV['APP_URL'],
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json'
            ],
        ]);

        $this->pdo = Helpers::getPdo();
        // Helpers::initDb($this->pdo, __DIR__ . '/../db-init.sql');
    }

    private function getToken(): string
    {
        $response = $this->http->get('/token');
        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['token'];
    }

    public function testGetToken()
    {
        $response = $this->http->get('/token');
        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('expires', $data);
    }

    public function testApiUnauthorized()
    {
        $response = $this->http->get('/api/items', [
            'headers' => [
                'Authorization' => '',
            ]
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }


    public function testGetItems()
    {
        $response = $this->http->get('/api/items', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());
        $items = json_decode($response->getBody()->getContents(), true);

        $dbItems = Helpers::dbQueryAll($this->pdo, 'select * from items');

        self::assertEquals($this->count($dbItems), $this->count($items));
    }

    public function testGetItem()
    {
        $dbItem = Helpers::dbQueryOne($this->pdo, 'select * from items limit 1');

        $response = $this->http->get("/api/item/{$dbItem['id']}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
            ]
        ]);

        self::assertEquals(200, $response->getStatusCode());
        $item = json_decode($response->getBody()->getContents(), true);

        self::assertEquals($dbItem, $item);
    }

    public function testNotExistingItem()
    {
        $id = -1;
        $response = $this->http->get("/api/item/$id", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
            ]
        ]);

        self::assertEquals(404, $response->getStatusCode());
        $item = json_decode($response->getBody()->getContents(), true);

    }

    public function testPostItems()
    {
        $newItem = [
            'name' => 'name999',
            'phone' => 'phone999',
            'key' => 'key999',
        ];

        $response = $this->http->post("/api/items", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
            ],
            'json' => $newItem,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $createdItem = json_decode($response->getBody()->getContents(), true);
        $dbItem = Helpers::dbQueryOne($this->pdo, "select * from items where id={$createdItem['id']}");
        self::assertTrue($newItem['name'] === $createdItem['name'] &&  $createdItem['name'] === $dbItem['name']);
    }

    public function testPostInvalidItem()
    {
        $newItem = [
            'name' => 'name888',
            'phone' => 'phone888',
            // 'key' => 'key888', // key is not null
        ];

        $response = $this->http->post("/api/items", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
            ],
            'json' => $newItem,
        ]);

        $this->assertEquals(422, $response->getStatusCode());
    }

}