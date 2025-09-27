<?php
require_once 'init_session.php';

if (empty($_SESSION['id']) || empty($_SESSION['username'])) {
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
