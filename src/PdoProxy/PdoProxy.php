<?php
declare(strict_types=1);

namespace BusFactor\PdoProxy;

use BusFactor\Pdo\PdoInterface;
use PDO;
use RuntimeException;

class PdoProxy implements PdoInterface
{
    /** @var callable */
    private $resolver;

    /** @var bool */
    private $resolved = false;

    /** @var PDO|null */
    private $pdo;

    /**
     * @param callable $resolver A callable that returns an instance of PDO
     */
    public function __construct(callable $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getPdo(): PDO
    {
        if ($this->resolved) {
            return $this->pdo;
        }
        $resolver = $this->resolver;
        $object = $resolver();
        if (!$object instanceof PDO) {
            throw new RuntimeException('The callable did not return a PDO instance.');
        }
        $this->pdo = $object;
        $this->resolved = true;
        return $this->pdo;
    }

    public function beginTransaction(): bool
    {
        return call_user_func_array([$this->getPdo(), 'beginTransaction'], func_get_args());
    }

    public function commit(): bool
    {
        return call_user_func_array([$this->getPdo(), 'commit'], func_get_args());
    }

    public function errorCode(): ?string
    {
        return call_user_func_array([$this->getPdo(), 'errorCode'], func_get_args());
    }

    public function errorInfo(): array
    {
        return call_user_func_array([$this->getPdo(), 'errorInfo'], func_get_args());
    }

    public function exec(string $statement)
    {
        return (int) call_user_func_array([$this->getPdo(), 'exec'], func_get_args());
    }

    public function getAttribute(int $attribute)
    {
        return call_user_func_array([$this->getPdo(), 'getAttribute'], func_get_args());
    }

    public static function getAvailableDrivers(): array
    {
        return call_user_func_array('PDO::getAvailableDrivers', func_get_args());
    }

    public function inTransaction(): bool
    {
        return call_user_func_array([$this->getPdo(), 'inTransaction'], func_get_args());
    }

    public function lastInsertId(?string $name = null): string
    {
        return call_user_func_array([$this->getPdo(), 'lastInsertId'], func_get_args());
    }

    public function prepare(string $statement, array $driverOptions = [])
    {
        return call_user_func_array([$this->getPdo(), 'prepare'], func_get_args());
    }

    public function query(string $statement, int $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $constructorArgs = [])
    {
        return call_user_func_array([$this->getPdo(), 'query'], func_get_args());
    }

    public function quote(string $string, int $parameterType = PDO::PARAM_STR)
    {
        return call_user_func_array([$this->getPdo(), 'quote'], func_get_args());
    }

    public function rollback(): bool
    {
        return call_user_func_array([$this->getPdo(), 'rollback'], func_get_args());
    }

    public function setAttribute(int $attribute, $value): bool
    {
        return call_user_func_array([$this->getPdo(), 'setAttribute'], func_get_args());
    }
}
