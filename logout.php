<?php
session_start();
session_destroy();

// XÓA COOKIE - CHỈ 2 DÒNG
setcookie('user_id', '', time() - 3600);
setcookie('username', '', time() - 3600);

header('location: login.php');
