<?php
require_once 'init_session.php';

if (empty($_SESSION['id']) || empty($_SESSION['username'])) {
    header('location: login.php');
    exit;
}

require_once 'models/UserModel.php';
$userModel = new UserModel();

$user = NULL;
$_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_id !== false && $_id !== null) {
    $user = $userModel->findUserById($_id);
}

if (!empty($_POST['submit'])) {
    $data = [
        'id' => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT),
        'name' => trim(strip_tags($_POST['name'] ?? '')), // Strip tags để loại HTML/JS
        'password' => $_POST['password'] ?? ''
    ];

    if ($_id !== null && $_id !== false) {
        $userModel->updateUser($data);
    } else {
        $userModel->insertUser($data);
    }
    header('location: list_users.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>

<body>
    <?php include 'views/header.php' ?>
    <div class="container">

        <?php if ($user || $_id === null) { ?>
            <div class="alert alert-warning" role="alert">
                User form
            </div>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($_id ?? ''); ?>">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input class="form-control" name="name" placeholder="Name" value='<?php echo htmlspecialchars($user[0]['name'] ?? ''); ?>'>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password">
                </div>

                <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php } else { ?>
            <div class="alert alert-success" role="alert">
                User not found!
            </div>
        <?php } ?>
    </div>
</body>

</html>