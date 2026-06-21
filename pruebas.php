<?php
$passwordform ="12345678";
echo password_hash($passwordform, PASSWORD_DEFAULT).'<br>';

echo PHP_EOL;

$passwordbd = password_hash($passwordform, PASSWORD_DEFAULT);


if (password_verify($passwordform, $passwordbd)) {
    echo 'Password is valid!';
} else {
    echo 'Invalid password.';
}


?>