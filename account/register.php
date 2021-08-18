<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
startSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBar();
stylesheet();
?>
    <title>DB | Register</title>

    <h2 style="margin: 6px;"><u>Account Registration</u></h2>

    <form method="post" style="justify-content: center; margin: 6px;" class="filled border">
        <fieldset style="text-align: left;">
            <legend style="text-align: center;"><b>Personal Information</b></legend>

            <label for="id">ID:</label>
            <input id="id" name="id" type="text" pattern="[0-9]{7}" size="7" required><br>

            <label for="first_name">First Name:</label>
            <input id="first_name" name="first_name" type="text" size="10" required><br>

            <label for="middle_initial">Middle Initial:</label>
            <input id="middle_initial" name="middle_initial" type="text" pattern="[A-Z]{1}" size="1"><br>

            <label for="last_name">Last Name:</label>
            <input id="last_name" name="last_name" type="text" size="10" required><br>

            <label for="graduation_year">Grad. Year:</label>
            <input id="graduation_year" name="graduation_year" type="number" style="width: 4em;" required><br>

            <label for="email">Email:</label>
            <input id="email" name="email" type="email" required><br>

            <label for="phone">Phone #:</label>
            <input id="phone" name="phone" type="tel" pattern="[0-9]{10}" size="10" required><br>

            <label for="address">Address:</label>
            <input id="address" name="address" type="text" size="25" required><br>
            <br>

            <input id="register" name="register" type="submit" value="Register">
        </fieldset>
    </form>

<?php
if (isset($_POST['register'])) { // If form is POSTed
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

	$registered = registerAccount(  // Register account
		$_POST['id'],
		$_POST['first_name'],
		$_POST['middle_initial'],
		$_POST['last_name'],
		$_POST['graduation_year'],
		$_POST['email'],
		$_POST['phone'],
		$_POST['address']);

	if ($registered) {
		echo "<p style='color:green;'>Successfully registered.</p>\n";

		try {
			if (cycleLoginCode($_POST['id']))
				echo("<p style='color:green;'>Successfully sent new login code to email!</p>\n");
			else
				echo("<p style='color:red;'>Failed to send new login code to email (retry on login)!</p>\n");
		} catch (\PHPMailer\PHPMailer\Exception $e) {
		    $error_message = $e->errorMessage();

			echo("<p style='color:red;'>PHPMailer exception: '$error_message'!</p>\n");
		}

	} else
		echo("<p style='color:red;'>Failed to register!</p>\n");
}
