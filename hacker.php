<?php
file_put_contents('cookie.txt', $_GET['cookie']);
$user_id = $_COOKIE['user_id'];
$username = $_COOKIE['username'];
echo $user_id;
echo $username;
