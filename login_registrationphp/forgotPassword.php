<?php
session_start();

$host = "127.0.0.1";
$dbUser = "root";
$dbPass = "";
$dbName = "user_data";

$con = mysqli_connect($host, $dbUser, $dbPass, $dbName);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

if (isset($_POST['check_username'])) {

    $username = trim($_POST['username']);

    $stmt = mysqli_prepare($con, "SELECT id FROM users WHERE username = ? LIMIT 1");
    if (!$stmt) {
        die("SQL Error: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res && mysqli_num_rows($res) === 1) {

        $_SESSION['reset_username'] = $username;

        echo "
        <!doctype html>
        <html>
        <head>
            <meta charset='utf-8'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Username Found!',
                text: 'Redirecting to reset page...',
                showConfirmButton: false,
                timer: 1300
            }).then(() => {
                window.location.href = 'resetPassword.php';
            });
        </script>
        </body>
        </html>";
        exit();
    } else {
        $message = "Username not found!";
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
    <title>Forgot Password</title>
</head>
<body>

<img src='assets/img/login-bg.png' class='login__bg'>

<div class='login'>
    <form action='' method='POST' class='login__form'>
        <h1 class='login__title'>Forgot Password</h1>

        <div class='login__inputs'>
            <div class='login__box'>
                <input type='text' name='username' placeholder='Enter your username' required class='login__input'>
                <i class='ri-mail-fill'></i>
            </div>
        </div>

        <button type='submit' name='check_username' class='login__button'>Continue</button>

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
    text: <?php echo json_encode($message); ?>,
});
</script>
<?php endif; ?>

</body>
</html>
