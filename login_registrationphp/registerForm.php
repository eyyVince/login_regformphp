<?php
session_start();

$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "user_data";

$con = mysqli_connect($host, $username, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

function showInvalidEmail() {
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
            icon: 'error',
            title: 'Invalid Email!',
            text: 'Please enter a valid email address.',
            showConfirmButton: true
        }).then(() => {
            window.location.href = 'registerForm.php';
        });
    </script>
    </body>
    </html>";
    exit();
}


if (isset($_POST['submit'])) {

    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    if(!filter_var($username, FILTER_VALIDATE_EMAIL)) {
      showInvalidEmail();
    }

    if(!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z]+\.[A-Za-z]{2,6}$/", $username)) {
      showInvalidEmail();
    }

    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
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
                icon: 'error',
                title: 'Invalid Email!',
                text: 'Please enter a valid email address.',
                showConfirmButton: true
            });
        </script>
        </body>
        </html>";
        exit();
    }

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

    if (mysqli_query($con, $sql)) {
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
                title: 'Account Added!',
                text: 'Returning to Registration...',
                showConfirmButton: false,
                timer: 1300
            }).then(() => {
                window.location.href = 'registerForm.php';
            });
        </script>
        </body>
        </html>";
        exit();
    } else {
        $message = "Error: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>


<!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" crossorigin="">
      <link rel="stylesheet" href="assets/css/register.css">

      <title>Registration</title>
   </head>
   <body>

      <?php if (!empty($message)): ?>
         <div class="alert">
         <?php echo $message; ?>
         </div>
      <?php endif; ?>

      <script>
         setTimeout(() => {
        const alertBox = document.querySelector('.alert');
        if (alertBox) alertBox.style.display = 'none';
         }, 3000);
      </script>

      <div class="register">
         <img src="assets/img/login-bg.png" alt="image" class="login__bg">

         <form action="" method="POST" class="reg__form">
            <h1 class="reg__title">Register Account</h1>

            <div class="reg__inputs">
               <div class="reg__box">
                  <input type="email" name="username" placeholder="Email ID" required class="reg__input">
                  <i class="ri-mail-fill"></i>
               </div>

               <div class="reg__box">
                  <input type="password" name="password" placeholder="Password" required class="reg__input">
                  <i class="ri-lock-2-fill"></i>
               </div>
            </div>
            
            <button type="submit" name="submit" class="reg__button">Register</button>

            <div class="page-transition">
            <div class="container form-container">
            <form>
            <div class="register__login">Already have an account? <a href="index.php">Login</a>
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

         </form>
      </div>
    </div>
   </body>
</html>
