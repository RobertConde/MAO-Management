<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "require/sql.php";

//$db = getDBConn();
//$db->query("CREATE DATABASE IF NOT EXISTS `mao-copy` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci; USE `mao-copy`; CREATE TABLE `login` (id` text NOT NULL, `code` text NOT NULL DEFAULT 'abc123', `time_cycled` timestamp NOT NULL DEFAULT current_timestamp(), `time_last_login` timestamp NULL DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; CREATE TABLE `people` (`id` text NOT NULL, `fname` text NOT NULL, `lname` text NOT NULL, `grade` int(11) NOT NULL, `email` text NOT NULL, `phone` text NOT NULL, `division` int(11) NOT NULL, `perms` int(11) NOT NULL DEFAULT 1, `time_registered` timestamp NOT NULL DEFAULT current_timestamp(), `time_updated` timestamp NULL DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ALTER TABLE `login` ADD UNIQUE KEY `id` (`id`) USING HASH; ALTER TABLE `people` ADD UNIQUE KEY `id` (`id`) USING HASH; COMMIT;");
//echo $db->error;

/*
$mysql_host = "localhost";
$mysql_database = "db";
$mysql_user = "user";
$mysql_password = "password";
# MySQL with PDO_MYSQL
$db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);

$query = file_get_contents("shop.sql");

$stmt = $db->prepare($query);

if ($stmt->execute()){
     echo "Success";
}else{
     echo "Fail"
}
 */
