<?php
header("Content-Type: application/json; charset=UTF-8");
include_once '../controllers/AuthController.php';

$auth = new AuthController();
$auth->logout();
?>
