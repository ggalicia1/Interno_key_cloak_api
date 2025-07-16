<?php

namespace App\Contracts\Realm;

interface IRealm
{
    public function keys(string $realm) : array;
}
