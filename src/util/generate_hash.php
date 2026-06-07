<?php
$passwords = [
    'admin'    => 'Admin123',
    'customer' => 'Customer123'
];

foreach ($passwords as $role => $password) {
    echo $role . ': ' . password_hash($password, PASSWORD_BCRYPT) . '<br>';
}
?>