<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/require/htmlSnippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/require/checks.php";
checkPerms();

require_once $_SERVER['DOCUMENT_ROOT'] . "/require/calendar.php";

//DEBUG
echo "SESSION<br>";
foreach ($_SESSION as $key => $value) {
	echo "Key: '$key'; Value: '$value'<br>";
}
echo "<br>", "POST<br>";
foreach ($_POST as $key => $value) {
	echo "Key: '$key'; Value: '$value'<br>";
}
echo "<br>", "GET<br>";
foreach ($_GET as $key => $value) {
	echo "Key: '$key'; Value: '$value'<br>";
}
echo "<br>", "MONTH<br>";
if (isset($_GET['month']))
	echo formatDateTimeSQL(new DateTime($_GET['month'])) . "<br>";
echo "<br>";





$sql_conn = getDBConn();
$prepare = $sql_conn->prepare("SELECT * FROM transactions WHERE time_paid >='2021-07-02 00:00:00' AND time_paid <= '2021-07-02 23:59:59' ORDER BY time_paid");
$prepare->execute();
$result = $prepare->get_result();

echo getTableFromResult($result);
?>


<html lang="en">
<form method="get">
	<label for="month-id">month:</label>
	<input id="month-id" name="month" type="month">
	<input type="submit">
</form>
</html>
<br>
<div class="tag">Test</div>
<div class="tag">Test</div>
<div class="tag">Test</div>
<div class="tag">Test</div>

