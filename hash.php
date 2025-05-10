<?php
$password = '1234'; // รหัสผ่านของ Admin
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
?>
