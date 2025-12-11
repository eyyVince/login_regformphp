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

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($con, "SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res && mysqli_num_rows($res) === 1) {
        $row = mysqli_fetch_assoc($res);

        if ($password === $row['password']) {
            $_SESSION['username'] = $username;
            
            echo "
            <!doctype html>
            <html>
            <head>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width,initial-scale=1'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful!',
                    text: 'Redirecting to dashboard...',
                    showConfirmButton: false,
                    timer: 1300
                }).then(() => {
                    window.location.href = 'dashboard.php';
                });
            </script>
            </body>
            </html>";
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <img src="assets/img/login-bg.png" alt="image" class="login__bg">
  <div class="login">
    <form action="index.php" method="POST" class="login__form">
      <h1 class="login__title">Login</h1>

      <div class="login__inputs">
        <div class="login__box">
          <input type="text" name="username" placeholder="Username" required class="login__input">
          <i class="ri-mail-fill"></i>
        </div>

        <div class="login__box">
          <input type="password" name="password" placeholder="Password" required class="login__input">
          <i class="ri-lock-2-fill"></i>
        </div>
      </div>

      <div class="login__forgot-container">
          <a href="forgotPassword.php" class="login__forgot">Forgot Password?</a>
      </div>

      <button type="submit" name="login" class="login__button">Login</button>

      <div class="page-transition">
      <div class="container form-container">
        <form>
        <div class="login__register">
        Don't have an account? <a href="registerForm.php">Register</a>
        </div>
        </form>
      </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
      document.body.classList.add("page-fade");
  });

  document.querySelectorAll("a").forEach(link => {
      link.addEventListener("click", function (e) {
          const href = this.getAttribute("href");
          if (!href.startsWith("#")) { 
              e.preventDefault(); 
              document.body.classList.add("fade-out");

              setTimeout(() => {
                  window.location = href;
              }, 300);
          }
      });
  });
  </script>


  <?php if (!empty($message)): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Login failed',
        text: <?php echo json_encode($message); ?>,
      });
    </script>
  <?php endif; ?>

</body>
</html>
