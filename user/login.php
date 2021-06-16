<?php
session_start();

$cycle_and_email_result = null;
$login_result = null;

if (isset($_SESSION['id']))
	header("Location: https://" . $_SERVER['HTTP_HOST'] . "/");
else if (isset($_POST['cycle_code'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/require/basicAccountManage.php";

	$cycle_and_email_result = cycleLoginCode($_POST['id']);
} else if(isset($_POST['login'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/require/sql.php";

    if (getDetail('login', 'code', $_POST['id']) == $_POST['code']) {
        require_once $_SERVER['DOCUMENT_ROOT'] . "/require/basicAccountManage.php";

	    updateLoginTime($_POST['id']);  // Update login time (in `login` table)

	    $_SESSION['id'] = $_POST['id']; // Login (session)

	    header("Refresh:0");    // Refresh page
    } else
        $login_result = false;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "require/htmlSnippets.php";

stylesheet();
navigationBar();
?>

<html>
<h2>MAO Account Login</h2>

<h3>1) Get a New Login Code <i>(If Necessary)</i></h3>
<form method="post" action="login.php">
	<label for="id">ID:</label>
	<input id="id" name="id" type="text" pattern="[0-9]{7}" required><br>
	<br>
	<input id="cycle_code" name="cycle_code" type="submit" value="Email Code">
</form>

<?php
if (!is_null($cycle_and_email_result)) {
	if ($cycle_and_email_result)
		echo("<p style=\"color:green;\">Successfully sent new login code to email! </p>\n");
	else
		echo("<p style=\"color:red;\">Failed to send new login code to email (retry)! </p>\n");
}
?>

<hr>

<h3>2) Login!</h3>
<form method="post" action="login.php">
	<label for="id">ID:</label>
    <input id="id" name="id" type="text" pattern="[0-9]{7}" required><br>
    <br>
    <label for="code">Login Code:</label>
    <input id="code" name="code" type="password" pattern="[0-9a-f]{6}" required><br>
    <br>
    <input id="login" name="login" type="submit" value="Login">
</form>
</html>

<?php
if (!is_null($login_result)) {
	if (!$login_result)
		echo("<p style=\"color:red;\">Invalid credentials/failed to login! </p>\n");
}
?>
