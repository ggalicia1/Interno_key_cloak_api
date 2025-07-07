<?php

namespace App\Contracts\Client;

use Illuminate\Support\Arr;

interface IClient
{
    public function clients(array $data) : array;
    public function clientById(array $data) : array;
    public function createClient(array $data): array;
    public function updateClient(array $data): array;
}
