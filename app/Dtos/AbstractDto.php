<?php

declare(strict_types=1);

namespace App\Dtos;

abstract class AbstractDto
{
    public function get(): self
    {
        return $this;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
