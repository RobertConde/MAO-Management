<?php
session_start();

$cycle_and_email_result = null;
$login_result = null;

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

if (isset($_SESSION['id']))
	header("Location: https://" . $_SERVER['HTTP_HOST'] . "/student/updateInfo");
else if (isset($_POST['cycle_code']))
	$cycle_and_email_result = cycleLoginCode($_POST['id']);
else if(isset($_POST['login'])) {
	if (getAccountDetail('login', 'code', $_POST['id']) == $_POST['code']) {
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

		updateLoginTime($_POST['id']);  // Update login time (in `login` table)

		$_SESSION['id'] = $_POST['id']; // Login (session)

		header("Location: https://" . $_SERVER['HTTP_HOST'] . "/"); // Go to index page
	} else
		$login_result = false;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();
?>

<html lang="en">
<title>Login</title>

<h2><u>Account Login</u></h2>

<form method="post" action="login">
    <fieldset>
        <legend><b>1) Get a New Login Code <i>(If Necessary)</i></b></legend>

        <label for="id">ID:</label>
        <input id="id" name="id" type="text" pattern="[0-9]{7}" required><br>
        <br>
        <input id="cycle_code" name="cycle_code" type="submit" value="Email Code">
    </fieldset>
</form>

<?php
if (!is_null($cycle_and_email_result)) {
	if ($cycle_and_email_result)
		echo("<p style=\"color:green;\">Successfully sent new login code to email! </p>\n");
	else
		echo("<p style=\"color:red;\">Failed to send new login code to email (retry)! </p>\n");
}
?>

<form method="post" action="login">
    <fieldset>
        <legend><b>2) Login!</b></legend>

        <label for="id">ID:</label>
        <input id="id" name="id" type="text" pattern="[0-9]{7}" required><br>
        <br>
        <label for="code">Login Code:</label>
        <input id="code" name="code" type="password" pattern="[0-9a-f]{6}" required><br>
        <br>
        <input id="login" name="login" type="submit" value="Login">
    </fieldset>
</form>
</html>

<?php
if (!is_null($login_result)) {
	if (!$login_result)
		echo("<p style=\"color:red;\">Invalid credentials/failed to login! </p>\n");
}
?>
