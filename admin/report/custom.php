<?php
/* Header */
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/checks.php";
checkPerms(OFFICER);

if (isset($_GET['table']))
	echo "<h3 style=\"text-align: center; margin: 6px\"><u>Table: <i>" . $_GET['table'] ."</i></u></h3>";

$table = "";
if (isset($_GET['table']))
	$table = $_GET['table'];

$order_by = "";
if (isset($_GET['order_by']))
    $order_by = $_GET['order_by'];
?>

<html>
<form id="table-form" method="get" style="text-align: center; margin: 6px;" class="noprint">
	<fieldset>
        <legend><b>Report Parameters</b></legend>

        <select id="tables" name="table" required style="margin: 4px">
            <option value="" disabled hidden selected>Choose a table...</option>
            <option value="people" <?php echo $table == "people" ? "selected" : "" ?>>People (Account Information)</option>
            <option value="login" <?php echo $table == "login" ? "selected" : "" ?>>Login (Login Codes)</option>
            <option value="payment_details" <?php echo $table == "payment_details" ? "selected" : "" ?>>Payment Details</option>
            <option value="transactions" <?php echo $table == "transactions" ? "selected" : "" ?>>Transactions</option>
        </select>
        <label for="tables" style="color: red;">*</label><br>

        <input id="order_by" name="order_by" type="text" placeholder="SQL Order By Expression" style="margin: 4px"
               value="<?php echo $order_by; ?>">
        <div class="tooltip"><i class="fa fa-question-circle"></i>
            <span class="tooltiptext">Beware of SQL Injection!</span>
        </div><br>

        <!--    <button onclick="location.href=''">Reset</button>-->
        <input type="submit" value="Get!">
    </fieldset>
</form>
</html>

<?php
if (isset($_GET['table']))
	echo surrTags('center', $order_by == "" ? getTableSQL($_GET['table']) : getTableSQL($_GET['table'], $order_by));
else
	die("<p style=\"color:red; text-align:center; margin:4px\">A table must be specified!</p>\n");
