<?php

namespace App\Contracts\Realm;

interface IRealm
{
    public function all() : array;
    public function keys(string $realm) : array;
    public function key(array $data) : array;
}
