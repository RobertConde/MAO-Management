<?php
$cycle_and_email_result = null;
$login_result = null;

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safeStartSession();

if (isset($_SESSION['id']))
	header("Location: https://" . $_SERVER['HTTP_HOST'] . "/student/info");
else if (isset($_POST['cycle_code']))
	try {
		$cycle_and_email_result = cycleLoginCode($_POST['id']);
	} catch (\PHPMailer\PHPMailer\Exception $e) {}  // TODO: error message
else if (isset($_POST['login'])) {
	if (getAccountDetail('login', 'code', $_POST['id']) == $_POST['code']) {
		$_SESSION['id'] = $_POST['id']; // Login (session)

		header("Location: https://" . $_SERVER['HTTP_HOST'] . "/"); // Go to index page
	} else
		$login_result = false;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safeStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();
?>

<title>DB | Login</title>

<h2 style="margin: 6px;"><u>Account Login</u></h2>

<form method="post" style="text-align: center; margin: 6px;" class="filled border">
    <fieldset style="text-align: left;">
        <legend style="text-align: center;"><b>Get a Login Code</b></legend>

        <label for="id">ID:</label>
        <input id="id" name="id" type="text" pattern="[0-9]{7}" size="7" required><br>
        <br>

        <input id="cycle_code" name="cycle_code" type="submit" value="Email Code">
    </fieldset>
</form><br>

<?php
if (!is_null($cycle_and_email_result)) {
	if ($cycle_and_email_result)
		echo("<p style='color:green;'>Successfully sent new login code to email! </p>\n");
	else
		echo("<p style='color:red;'>Failed to send new login code to email (retry)! </p>\n");
}
?>

<form method="post" style="text-align: center; margin: 6px;" class="filled border">
    <fieldset style="text-align: left;">
        <legend style="text-align: center;"><b>Login!</b></legend>

        <label for="id">ID:</label>
        <input id="id" name="id" type="text" pattern="[0-9]{7}" size="7" required><br>

        <label for="code">Code:</label>
        <input id="code" name="code" type="password" pattern="[0-9A-F]{6}" size="6" required><br>
        <br>

        <input id="login" name="login" type="submit" value="Login">
    </fieldset>
</form>

<?php
if ($login_result === false)
	echo("<p style='color:red;'>Invalid credentials/failed to log in! </p>\n");
?>
