<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Minimal service container for dependency injection.
 */
class Container
{
    /** @var array<string, mixed> */
    private array $services = [];

    /** @var array<string, callable(self): mixed> */
    private array $factories = [];

    /**
     * @param callable(self): mixed $factory
     */
    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
        unset($this->services[$id]);
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]) || isset($this->factories[$id]);
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->services)) {
            return $this->services[$id];
        }

        if (!isset($this->factories[$id])) {
            throw new RuntimeException(sprintf('Service "%s" is not registered.', $id));
        }

        $this->services[$id] = ($this->factories[$id])($this);

        return $this->services[$id];
    }
}
