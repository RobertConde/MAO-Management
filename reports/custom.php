<?php
/* Header */
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/require/htmlSnippets.php";
stylesheet();
navigationBar();

//stylizedHeader();

require_once $_SERVER['DOCUMENT_ROOT'] . "/require/checks.php";
checkPerms(10);

/* Body */
if (isset($_GET['table']))
	echo surrTags('center', isset($_GET['sort']) ? getTableSQL($_GET['table'], $_GET['sort']) : getTableSQL($_GET['table']));
else
	die("<p style=\"color:red;\">A table must be specified!</p>\n");

//echo getStylizedTablePage($_GET['table']);
