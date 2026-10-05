<?php

session_start();

$_SESSION = array();

session_unset();

session_destroy();

setcookie(
    session_name(),
    '',
    time() - 3600,
    '/'
);

header("Location: index.php?pagina=login");

exit;

?>