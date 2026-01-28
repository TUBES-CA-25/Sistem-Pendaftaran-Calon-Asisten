<?php
namespace App\Controllers;

use App\Model\UserModel;
use App\Core\Controller;
class RoleUserController extends Controller {
    public static function getRole() : array {
        $user = new UserModel();
        $id = $_SESSION['user']['id'];
        $user = $user->getUser($id)['role'];
        return $user == null ? [] : $user;
    }
}