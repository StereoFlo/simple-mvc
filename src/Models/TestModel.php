<?php

namespace Models;

use Core\Model;

class TestModel extends Model
{
    public static function create()
    {
        return new self();
    }

    public function getMedia()
    {
        return $this->get('media');
    }
}