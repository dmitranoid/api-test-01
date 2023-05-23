<?php

declare(strict_types=1);

namespace Tests\Utils;

use http\Params;

class Helpers
{
    static function loadTestEnv(string $envFile = 'test.env'): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../', $envFile);
        $dotenv->load();
    }

    static function getPdo(): \PDO
    {
        $sqlitePath = realpath(__DIR__ . '/../../database/');
        if (!file_exists($sqlitePath . "/{$_ENV['DB_NAME']}")) {
            die($sqlitePath . "/{$_ENV['DB_NAME']} not found");
        }
        $pdo = new \PDO(
            'sqlite:' . $sqlitePath . "/{$_ENV['DB_NAME']}",
            '',
            '',
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA journal_mode = MEMORY');
        return $pdo;
    }

    /**
     * exec sql script
     *
     * sql commands divider is ###
     * @param \PDO $pdo
     * @param $scriptFile
     * @return void
     */
    public static function initDb(\PDO $pdo, $scriptFile): void
    {
        $script = file_get_contents($scriptFile);
        $queries = explode('###', $script);
        foreach ($queries as $query) {
            $pdo->query($query);
        }
    }

    public static function dbQueryOne(\PDO $pdo, $query, $params = [])
    {
        $sth = $pdo->prepare($query);
        $sth->execute($params);
        return $sth->fetch();
    }

    public static function dbQueryAll(\PDO $pdo, $query, $params = [])
    {
        $sth = $pdo->prepare($query);
        $sth->execute($params);
        return $sth->fetchAll();
    }

}