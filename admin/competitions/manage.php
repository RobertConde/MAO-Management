<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);

// Establishing the database connection
$sql_conn = getDBConn();
if (!$sql_conn) {
    // Log the error if connection fails
    error_log("Failed to connect to database: " . mysqli_connect_error());
    die("Database connection failed. Check error log for details.");
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

$comp = null;
if (isset($_GET['comp_name']) && existsComp($_GET['comp_name'])) {
    $comp = $_GET['comp_name'];
    // Fetch competition details including hidden state
    $start_date = getAssociatedCompInfo($comp, 'start_date');
    $end_date = getAssociatedCompInfo($comp, 'end_date');
    $payment_id = getAssociatedCompInfo($comp, 'payment_id');
    $check_status_forms = (getAssociatedCompInfo($comp, 'show_forms') ?? true) ? 'checked' : '';
    $check_status_bus = (getAssociatedCompInfo($comp, 'show_bus') ?? true) ? 'checked' : '';
    $check_status_room = (getAssociatedCompInfo($comp, 'show_room') ?? true) ? 'checked' : '';
    $check_status_hidden = (getAssociatedCompInfo($comp, 'hidden') ?? true) ? 'checked' : '';
    $description = getDetail('competitions', 'description', 'competition_name', $comp);
} else {
    // Initialize variables if no competition is selected
    $start_date = '';
    $end_date = '';
    $payment_id = '';
    $check_status_forms = '';
    $check_status_bus = '';
    $check_status_room = '';
    $check_status_hidden = '';
    $description = '';
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Get form data
        $comp_name = $_POST['comp_name'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $payment_id = $_POST['payment_id'];
        $show_forms = isset($_POST['forms']) ? 1 : 0;
        $show_bus = isset($_POST['bus']) ? 1 : 0;
        $show_room = isset($_POST['room']) ? 1 : 0;
        $hidden = isset($_POST['hidden']) ? 1 : 0;
        $description = $_POST['info'];

        // Update competition details in the database
        $update_stmt = $sql_conn->prepare("UPDATE competitions SET start_date=?, end_date=?, payment_id=?, show_forms=?, show_bus=?, show_room=?, hidden=?, description=? WHERE competition_name=?");
        $update_stmt->bind_param("ssiiiiiss", $start_date, $end_date, $payment_id, $show_forms, $show_bus, $show_room, $hidden, $description, $comp_name);
        if ($update_stmt->execute()) {
            echo "<script>alert('Competition updated successfully');</script>";
            echo "<script>window.location = window.location.href;</script>"; // Refresh the page after the successful update
        } else {
            echo "Error updating competition: " . $update_stmt->error;
        }
        $update_stmt->close();
    }
}
?>

<title>MAO | Competitions</title>

<h2 style="margin: 6px;"><u>Competitions</u></h2>

<div style="display: flex; justify-content: center; flex-direction: column; margin: auto; width: fit-content;">
    <form method="get" class="filled border" style="text-align: center; margin: 6px;">
        <fieldset>
            <legend><b>Competition</b></legend>
    
            <select name="comp_name" onchange="this.form.submit()" style="margin-bottom: 6px;">
                <option selected disabled hidden></option>
                <?php
                $comp_names_stmt = $sql_conn->prepare("SELECT competition_name FROM competitions");
                $comp_names_stmt->bind_result($curr_comp_name);
                $comp_names_stmt->execute();
    
                while ($comp_names_stmt->fetch()) {
                    if ($curr_comp_name == $comp) {
                        echo "<option value='" . htmlspecialchars($curr_comp_name) . "' selected>" . htmlspecialchars($curr_comp_name) . "</option>";
                    } else {
                        echo "<option value='" . htmlspecialchars($curr_comp_name) . "'>" . htmlspecialchars($curr_comp_name) . "</option>";
                    }
                }
                $comp_names_stmt->close(); // Close statement after use
                ?>
            </select>
        </fieldset>
    </form>
    
    <form method="post" class="filled border" style="text-align: center;">
        <fieldset>
            <h4 style="margin-top: 2px;"><u>Information</u></h4>
    
            <div style="display: inline-block; text-align: left;">
                <label for="comp_name">Competition:</label>
                <input id="comp_name" name="comp_name" type="text" required
                       value="<?php echo htmlspecialchars($comp); ?>"
                    <?php if (!is_null($comp)) echo 'disabled'; ?>>
                <?php
                if (!is_null($comp))
                    echo "<input name='comp_name' type='hidden' value='" . htmlspecialchars($comp) . "'>";
                ?><br>
    
                <label for="start_date">Start Date:</label>
                <input id="start_date" name="start_date" type="date"
                       value="<?php echo htmlspecialchars($start_date); ?>"><br>
    
                <label for="end_date">End Date:</label>
                <input id="end_date" name="end_date" type="date"
                       value="<?php echo htmlspecialchars($end_date); ?>"><br>
    
                <label for="payment_id">Payment ID:</label>
                <select id="payment_id" name="payment_id">
                    <option selected></option>
                    <?php
                    $payments_stmt = $sql_conn->prepare("SELECT payment_id FROM payment_details");
                    $payments_stmt->bind_result($curr_payment_id);
                    $payments_stmt->execute();
    
                    while ($payments_stmt->fetch()) {
                        if ($curr_payment_id == $payment_id) {
                            echo "<option value='" . htmlspecialchars($curr_payment_id) . "' selected>" . htmlspecialchars($curr_payment_id) . "</option>";
                        } else {
                            echo "<option value='" . htmlspecialchars($curr_payment_id) . "'>" . htmlspecialchars($curr_payment_id) . "</option>";
                        }
                    }
                    $payments_stmt->close(); // Close statement after use
                    ?>
                </select>
            </div>
            <br>
    
            <h4><u>Show Fields</u><br></h4>
    
            <div style="display: inline-block; text-align: left;">
                <input id="forms" name="forms" type="checkbox" <?php echo $check_status_forms ?>>
                <label for="forms">Forms Given/Submitted</label><br>
    
                <input id="bus" name="bus" type="checkbox" <?php echo $check_status_bus ?>>
                <label for="bus">Bus #</label><br>
    
                <input id="room" name="room" type="checkbox" <?php echo $check_status_room ?>>
                <label for="room">Room #</label><br>
    
                <input id="hidden" name="hidden" type="checkbox" <?php echo $check_status_hidden ?>>
                <label for="hidden">Hidden</label><br>
            </div>
            <br>
    
            <h4><u>Description</u></h4>
            <textarea id="info" name="info" rows="10" cols="50"><?php echo htmlspecialchars($description); ?></textarea><br>
            <br>
            <input id="create" name="create" type="submit" value="Create"
                   style="color: green; float: left;" <?php if (!is_null($comp)) echo 'disabled'; ?>>
            <input id="update" name="update" type="submit" value="Update"
                   style="color: blue;" <?php if (is_null($comp)) echo 'disabled'; ?>>
            <input id="delete" name="delete" type="submit" value="Delete"
                   style="color: red; float: right;" <?php if (is_null($comp)) echo 'disabled'; ?>>
        </fieldset>
    </form>
</div>