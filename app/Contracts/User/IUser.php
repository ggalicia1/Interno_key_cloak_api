<?php

namespace App\Contracts\User;

interface IUser
{
    public function users(array $data) : array;
    public function userById(array $data) : array;
    public function create(array $data) : array;
    public function update(string $reaml, string $user_id, array $data) : array;
    public function userCredential(string $user_id, array $data) : array;
}
