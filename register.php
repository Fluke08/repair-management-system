
<?php
session_start();


if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/register.css">
</head>

<body>

    <!-- Header -->
 


    <!-- Login -->
    <main class="form-signin text-center">

        <form action="register_db.php" method="POST">

                <div class="text-center mb-4">
                <div class="login-icon mb-3">
                    <i class="bi bi-tools"></i>
                </div>

                <h1 class="h3 fw-bold mb-2">
                    Welcome !
                </h1>

                <p class="text-secondary mb-0">
                    register to manage your repair requests
                </p>
            </div>

            <?php if (isset($_SESSION['success'])) { ?>
                <div class="alert alert-success" role="alert">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php } ?>

            <?php if (isset($_SESSION['error'])) { ?>
                <div class="alert alert-success" role="alert">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php } ?>




            <div class="form-floating">
                <input type="email" class="form-control my-2" name="email" id="floatingInput"
                    placeholder="name@example.com">

                <label for="floatingInput">
                    Email address
                </label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control my-2" name="username" id="floatingInput"
                    placeholder="enter your username">

                <label for="floatingInput">
                    username
                </label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control my-2" name="password" id="floatingPassword"
                    placeholder="Password">

                <label for="floatingPassword">
                    Password
                </label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control my-2" name="confirm_password" id="floatingPassword"
                    placeholder="Confirm Password">

                <label for="floatingPassword">
                    Confirm Password
                </label>
            </div>


        <div class="text-center mb-3">
    <p class="text-secondary small mb-0">
        มีบัญชีแล้วใช่ไหม?
        <a href="login.php" class="text-primary fw-semibold text-decoration-none">
            Login
        </a>
        เพื่อเข้าสู่ระบบ
    </p>
</div>


            <button class="w-100 btn btn-lg btn-primary" name="register" type="submit">
                Sign in
            </button>

            <p class="mt-5 mb-3 text-muted">
                © 2017–2021
            </p>

        </form>

    </main>


    <!-- Footer -->
    <div class="container">
        <?php require_once __DIR__ . '/include/footer.php'; ?>


    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>