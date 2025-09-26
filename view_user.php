<?php
require_once 'models/UserModel.php';
$userModel = new UserModel();

$user = NULL;
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id !== false && $id !== null) {
    $user = $userModel->findUserById($id);
}

if (!empty($_POST['submit'])) {
    if ($id !== null) {
        $userModel->updateUser($_POST);
    } else {
        $userModel->insertUser($_POST);
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

        <?php if ($user || $id === null) { ?>
            <div class="alert alert-warning" role="alert">
                User profile
            </div>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id ?? ''); ?>">
                <div class="form-group">
                    <label for="name">Name</label>
                    <span><?php if (!empty($user[0]['name'])) echo htmlspecialchars($user[0]['name']); ?></span>
                </div>
                <div class="form-group">
                    <label for="fullname">Fullname</label>
                    <span><?php if (!empty($user[0]['fullname'])) echo htmlspecialchars($user[0]['fullname']); ?></span>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <span><?php if (!empty($user[0]['email'])) echo htmlspecialchars($user[0]['email']); ?></span>
                </div>
            </form>
        <?php } else { ?>
            <div class="alert alert-success" role="alert">
                User not found!
            </div>
        <?php } ?>
    </div>
</body>

</html>