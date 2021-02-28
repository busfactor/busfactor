<?php

declare(strict_types=1);

namespace BusFactor\Pdo;

use PDO;
use PDOStatement;

interface PdoInterface
{
    public function getPdo(): PDO;

    public function beginTransaction(): bool;

    public function commit(): bool;

    public function errorCode(): ?string;

    public function errorInfo(): array;

    /** @return int|bool */
    public function exec(string $statement);

    /** @return mixed|null */
    public function getAttribute(int $attribute);

    public static function getAvailableDrivers(): array;

    public function inTransaction(): bool;

    public function lastInsertId(?string $name = null): string;

    /** @return PDOStatement|bool */
    public function prepare(string $statement, array $driverOptions = []);

    /** @return PDOStatement|bool */
    public function query(string $statement, int $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $constructorArgs = []);

    /** @return string|bool */
    public function quote(string $string, int $parameterType = PDO::PARAM_STR);

    public function rollBack(): bool;

    public function setAttribute(int $attribute, $value): bool;
}
