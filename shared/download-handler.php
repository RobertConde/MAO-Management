<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";

if (isset($_GET['filename'])) {
	$filename = $_GET['filename'];

	// ^[0-9A-F]*\.[a-zA-Z0-9]*$

	$filepath = $_SERVER['DOCUMENT_ROOT'] . "/../downloads/$filename";
	if (file_exists($filepath)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filepath));
		readfile($filepath);
		exit;
	}
}

if (isset($_GET['ref']))
	redirect($_GET['ref']);
else
	die('<script>window.close();</script>');
