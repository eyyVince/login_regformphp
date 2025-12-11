<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['reset_username'])) {
    header("Location: forgotPassword.php");
    exit();
}

$username = $_SESSION['reset_username'];

$host = "127.0.0.1";
$dbUser = "root";
$dbPass = "";
$dbName = "user_data";

$con = mysqli_connect($host, $dbUser, $dbPass, $dbName);
if (!$con) {
    die("DB Connection Error: " . mysqli_connect_error());
}

$message = "";

if (isset($_POST['update_password'])) {

    $new_pass = trim($_POST['password']);

    if ($new_pass === "") {
        $message = "Password cannot be empty.";
    } else {

        $stmt = mysqli_prepare($con, "UPDATE users SET password = ? WHERE username = ?");
        
        if (!$stmt) {
            $message = "SQL Prepare Error: " . mysqli_error($con);
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $new_pass, $username);

            if (mysqli_stmt_execute($stmt)) {

                unset($_SESSION['reset_username']);

                echo "
                <html>
                <head>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                </head>
                <body>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Updated!',
                        text: 'Redirecting to login...',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                </script>
                </body>
                </html>";
                exit();

            } else {
                $message = 'SQL Execution Error: ' . mysqli_stmt_error($stmt);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='assets/css/styles.css'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <title>Reset Password</title>
</head>

<body>

<img src='assets/img/login-bg.png' class='login__bg'>

<div class='login'>
    <form action='' method='POST' class='login__form'>
        <h1 class='login__title'>Reset Password</h1>

        <div class='login__inputs'>
            <div class='login__box'>
                <input type='password' name='password' placeholder='Enter new password' required class='login__input'>
                <i class='ri-lock-password-fill'></i>
            </div>
        </div>

        <button type='submit' name='update_password' class='login__button'>Update Password</button>

        <div class='login__register'>
            Back to <a href='index.php'>Login</a>
        </div>
    </form>
</div>

<?php if (!empty($message)): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: <?= json_encode($message); ?>
});
</script>
<?php endif; ?>

</body>
</html>
