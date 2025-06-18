<?php
namespace App\Interfaces;

interface UserRepositoryInterface
{
    // public function getAllUsers();
    // public function storeUser(array $user);
    // public function storeUserBranch(array $branch_id,$user_id);
    // public function storeUserCategory(array $category_ids,$user_id);
    // public function getUserById($user_id);
    // public function updateUser( array $data,$user_id);
    // public function deleteUser($user_id);

    public function saveLog($data);
}
