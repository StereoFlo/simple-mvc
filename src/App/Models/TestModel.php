<?php

namespace App\Models;

use Core\Model;

class TestModel extends Model
{
    /**
     * @return self
     * @throws \Exception
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getMedia(): array
    {
        $test = $this->where('id', 1)->get('test');
        return $test;
    }
}