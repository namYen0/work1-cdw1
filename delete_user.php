<?php
require_once 'models/UserModel.php';
$userModel = new UserModel();

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $userModel->deleteUserById($id); // Delete existing user
}
header('location: list_users.php');
