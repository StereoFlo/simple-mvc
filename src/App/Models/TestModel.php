<?php

namespace Models;

use Core\Model;

class TestModel extends Model
{
    /**
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @return array
     */
    public function getMedia(): array
    {
        return [];
    }
}