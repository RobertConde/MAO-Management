<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();
?>

<html lang="en">
<title>Register</title>

<h2><u>Account Registration</u></h2>
<form method="post" action="register">
    <fieldset>
        <legend><b>Account Information</b></legend>

        <label for="id">ID:</label>
        <input id= "id" name="id" type="text" pattern="[0-9]{7}" required><br>
        <br>
        <label for="fname">First Name:</label>
        <input id="fname" name="fname" type="text" required><br>
        <br>
        <label for="lname">Last Name:</label>
        <input id="lname" name="lname" type="text" required><br>
        <br>
        <label for="grade">Grade:</label>
        <select id="grade" name="grade" required>
            <option disabled selected></option>
            <option value="6">6th Grade</option>
            <option value="7">7th Grade</option>
            <option value="8">8th Grade</option>
            <option value="9">9th Grade</option>
            <option value="10">10th Grade</option>
            <option value="11">11th Grade</option>
            <option value="12">12th Grade</option>
            <option value="0">Not a Student</option>
        </select><br>
        <br>
        <label for="email">Email
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Will be used to send you <i>login codes</i> for your account!</span>
            </div>:
        </label>
        <input id="email" name="email" type="email" required><br>
        <br>
        <label for="phone">Phone Number
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Must be a 10-digit US phone number. Only type the digits!</span>
            </div>:
        </label>
        <input id="phone" name="phone" type="tel" pattern="[0-9]{10}" required><br>
        <br>
        <label for="division">Division
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Select the division you <i>will</i> compete the most this competition cycle.</span>
            </div>:
        </label>
        <select id="division" name="division" required>
            <option disabled selected></option>
            <option value="1">Algebra I</option>
            <option value="2">Geometry</option>
            <option value="3">Algebra II</option>
            <option value="4">Precalculus</option>
            <option value="5">Calculus</option>
            <option value="6">Statistics</option>
            <option value="0">Not a Student</option>
        </select>
    </fieldset><br>
	<br>
    <input id="register" name="register" type="submit" value="Register">
</form>
</html>

<?php
if (isset($_POST['register'])) { // If form is POSTed
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

    $registered = registerAccount(  // Register account
            $_POST['id'],
            $_POST['fname'],
            $_POST['lname'],
            $_POST['grade'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['division']);

    if ($registered) {
	    echo "<p style=\"color:green;\">Successfully registered.</p>\n";

	    if (cycleLoginCode($_POST['id']))
		    echo("<p style=\"color:green;\">Successfully sent new login code to email!</p>\n");
	    else
	        echo("<p style=\"color:red;\">Failed to send new login code to email (retry on login)!</p>\n");

    } else
	    echo("<p style=\"color:red;\">Failed to register!</p>\n");
}
