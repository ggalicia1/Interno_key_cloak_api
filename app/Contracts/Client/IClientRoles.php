<?php

namespace App\Contracts\Client;

interface IClientRoles
{
    public function clientRoles(array $data) : array;
    public function clientRoleByName(array $data) : array;
    public function createClientRole(array $date) : array;
    public function updateClientRole(array $date) : array;
    public function deleteClientRole(array $date) : array;
}
