<?php
return [
    '^\/$'                                          => 'index/index',
    '^\/index\/test\/([a-z]+)\/([a-z]+)\/([a-z]+)$' => 'index/test/$1/$2/$3',
];