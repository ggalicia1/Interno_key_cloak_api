<?php

namespace App\Contracts\User;

interface IUserRole
{
    public function roles(array $data) : array;
    public function addClientRole(array $data) : array;
    public function removeClientRole(string $realm, string $user_uuid, string $client_uuid, string $role_name) : array;
}
