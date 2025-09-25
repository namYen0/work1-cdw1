<?php
session_start();
require_once 'models/UserModel.php';

// Verify CSRF token
if (empty($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['message'] = 'CSRF token invalid!';
    header('location: list_users.php');
    exit();
}

$userModel = new UserModel();
$id = NULL;

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $userModel->deleteUserById($id);
}

header('location: list_users.php');
