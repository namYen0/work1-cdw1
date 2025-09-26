<?php
session_start();

if (empty($_SESSION['id'])) {
    header('location: login.php');
    exit;
}

require_once 'models/UserModel.php';
$userModel = new UserModel();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id !== false && $id !== null) {
    $userModel->deleteUserById($id);
}

header('location: list_users.php');
exit;
