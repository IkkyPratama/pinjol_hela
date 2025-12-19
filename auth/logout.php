<?php
include '../includes/functions.php';

session_destroy();
redirect('../auth/login.php');
?>