<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}
$username = $_SESSION["username"];

$conn = new mysqli("localhost", "root", "", "user_data");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_employee"])) {
    $full_name  = $_POST["full_name"];
    $usernameE  = $_POST["username"];
    $password   = $_POST["password"];
    $phone      = $_POST["phone"];
    $address    = $_POST["address"];
    $department = $_POST["department"];
    $position   = $_POST["position"];
    $date_hired = $_POST["date_hired"];
    $status     = $_POST["status"];
    $role       = $_POST["role"];

    $sql = "INSERT INTO employee_data 
    (full_name, username, password, phone, address, department, position, date_hired, status, role) 
    VALUES 
    ('$full_name', '$usernameE', '$password', '$phone', '$address', '$department', '$position', '$date_hired', '$status', '$role')";

    if ($conn->query($sql)) {
        $success = "Employee added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_employee"])) {
    $id = $_POST["delete_id"];
    $conn->query("DELETE FROM employee_data WHERE id = $id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    height: 100vh;
    display: flex;
    flex-direction: column;
}

.top-bar {
    width: 100%;
    background: #1f2a40;
    color: #ffffff;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.welcome { font-size: 20px; font-weight: bold; }
.clock { text-align: right; }
#current-time { font-size: 22px; font-weight: bold; }
.main-panel {
    flex: 1; width: 90%; margin: 25px auto;
    background: white; border-radius: 10px; padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.tabs { display: flex; border-bottom: 2px solid #ddd; }
.tab { padding: 10px 25px; cursor: pointer; font-weight: 600; }
.tab.active { border-bottom: 3px solid #1f2a40; }
.content-section { display: none; }
.content-section.active { display: block; }
input, select {
    padding: 8px; width: 100%; margin-bottom: 10px;
    border: 1px solid #bbb; border-radius: 6px;
}
button {
    padding: 10px 20px; border: none; background: #1f2a40;
    color: white; border-radius: 6px; cursor: pointer;
}
.logout-btn {
    background: #d9534f;
}
.employee-table {
    width: 100%; border-collapse: collapse; margin-top: 10px;
}
.employee-table th, .employee-table td {
    border: 1px solid #ccc; padding: 8px; text-align: left;
}
</style>
</head>
<body>

<div class="top-bar">
    <div class="welcome">Welcome, <?= htmlspecialchars($username) ?></div>
    <div class="clock">
        <div id="current-time">--:--:--</div>
        <div id="current-date"></div>
    </div>
</div>

<div class="main-panel">

    <div class="tabs">
        <div class="tab active" data-target="add">Add Employee</div>
        <div class="tab" data-target="delete">Delete Employee</div>
        <div class="tab" style="margin-left:auto;" data-target="payroll">Payroll</div>
    </div>

    <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <div id="add" class="content-section active">
        <h2>Add Employee</h2>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="password" placeholder="Password" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="department" placeholder="Department" required>
            <input type="text" name="position" placeholder="Position" required>
            <input type="date" name="date_hired" required>

            <select name="status">
                <option>Active</option>
                <option>Inactive</option>
            </select>

            <select name="role">
                <option>Employee</option>
                <option>Manager</option>
                <option>Admin</option>
            </select>

            <button type="submit" name="add_employee">Add Employee</button>
        </form>
    </div>

    <div id="delete" class="content-section">
        <h2>Employee List</h2>

        <table class="employee-table">
            <tr>
                <th>ID</th><th>Name</th><th>Department</th><th>Position</th><th>Action</th>
            </tr>

            <?php
            $result = $conn->query("SELECT * FROM employee_data ORDER BY id DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row["id"] ?></td>
                <td><?= $row["full_name"] ?></td>
                <td><?= $row["department"] ?></td>
                <td><?= $row["position"] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                        <button name="delete_employee" class="logout-btn">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div id="payroll" class="content-section">
    <h2>Payroll Panel</h2>

    <div style="display:flex; gap:20px; margin-top:20px;">

        <div style="flex:1; background:#f8f8f8; padding:20px; border-radius:10px; box-shadow:0 3px 8px rgba(0,0,0,0.1);">
            <h3>Employee Payroll</h3>

            <form id="payrollForm">

                <label>Select Employee</label>
                <select id="employeeSelect" required>
                    <option value="">-- Select Employee --</option>
                    <?php
                    $empList = $conn->query("SELECT id, full_name FROM employee_data");
                    while ($emp = $empList->fetch_assoc()):
                    ?>
                        <option value="<?= $emp['full_name'] ?>"><?= $emp['full_name'] ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Gross Pay</label>
                <input type="number" id="grossPay" placeholder="Enter Gross Pay" required>

                <label>Deductions</label>
                <input type="number" id="deductions" placeholder="Enter Deductions" required>

                <label>Net Pay</label>
                <input type="number" id="netPay" readonly style="background:#e9e9e9;">

                <button type="button" onclick="calculatePayroll()">Calculate Payroll</button>
            </form>
        </div>


        <div style="flex:1; background:white; padding:20px; border-radius:10px; box-shadow:0 3px 8px rgba(0,0,0,0.1);">
            <h3 style="text-align:center;">Payslip</h3>

            <div style="text-align:center; margin-bottom:10px;">
                <img src="company_logo.png" style="width:100px; height:auto;">
            </div>

            <div id="payslipContent">
                <p><strong>Employee:</strong> <span id="ps_employee">---</span></p>
                <p><strong>Gross Pay:</strong> ₱<span id="ps_gross">0.00</span></p>
                <p><strong>Deductions:</strong> ₱<span id="ps_deduct">0.00</span></p>
                <p><strong>Net Pay:</strong> ₱<span id="ps_net">0.00</span></p>

                <hr style="margin:20px 0; border:1px solid #000;">

                <h3 style="text-align:center;">Total Salary: ₱<span id="ps_total">0.00</span></h3>
            </div>
        </div>
    </div>
</div>


</div>

<div style="position:fixed; bottom:20px; left:20px;">
    <form action="index.php" method="POST">
        <button class="logout-btn">Logout</button>
    </form>
</div>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById("current-time").innerText = now.toLocaleTimeString();
    document.getElementById("current-date").innerText = now.toDateString();
}
setInterval(updateClock, 1000); updateClock();

document.querySelectorAll(".tab").forEach(tab => {
    tab.addEventListener("click", () => {
        document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
        document.querySelectorAll(".content-section").forEach(c => c.classList.remove("active"));

        tab.classList.add("active");
        document.getElementById(tab.dataset.target).classList.add("active");
    });
});

function calculatePayroll() {
    const name = document.getElementById("employeeSelect").value;
    const gross = parseFloat(document.getElementById("grossPay").value) || 0;
    const deduct = parseFloat(document.getElementById("deductions").value) || 0;

    const net = gross - deduct;

    document.getElementById("netPay").value = net.toFixed(2);

    document.getElementById("ps_employee").innerText = name || "---";
    document.getElementById("ps_gross").innerText = gross.toFixed(2);
    document.getElementById("ps_deduct").innerText = deduct.toFixed(2);
    document.getElementById("ps_net").innerText = net.toFixed(2);
    document.getElementById("ps_total").innerText = net.toFixed(2);
}

</script>


</body>
</html>
