<?php

namespace App\Contracts\User;

interface IUser
{
    public function users(array $data) : array;
    public function userById(array $data) : array;
    public function create(array $data) : array;
    public function update(string $reaml, string $user_id, array $data) : array;
    public function userCredential(string $user_id, array $data) : array;
    public function search(array $data) : array;
    public function retrieveRealmRoles(array $data) : array;
    public function joinGroup(string $realm, string $user_id, string $group_id) : array;
    public function leaveGroup(string $realm, string $user_id, string $group_id) : array;
    public function retrieveGroups(string $realm, string $user_id, array $criteria) : array;
}
