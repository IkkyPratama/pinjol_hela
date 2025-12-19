<?php
include 'functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}
?>