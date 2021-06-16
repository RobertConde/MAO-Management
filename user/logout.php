<?php
session_start();

session_destroy();

header("Location: https://" . $_SERVER['HTTP_HOST'] . "/user/login.php");
