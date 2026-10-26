<?php   

    session_start();
     require __DIR__ . '/config/database.php';
    $minLength = 6;

    if(isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $confirmpassword = $_POST['confirm_password'];
    }

    if (empty($username)) {
        $_SESSION['error'] = "Please enter you username";
        header("location: register.php");
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address";
        header("location: register.php");
    } else if (strlen($password) < $minLength) {
        $_SESSION['error'] = "Please enter a valid password";
        header("location: register.php");
    } else if ($password !== $confirmpassword) {
                $_SESSION["error"] = "Your password do not match";
        header("location: register.php");
    } else { 
        
        $checkUsername = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $checkUsername->execute([$username]);   
        $userNameExists = $checkUsername->fetchColumn();

              $checkEmail = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkEmail->execute([$email]);
        $userEmailExists = $checkEmail->fetchColumn();

        if ($userNameExists) {
            $_SESSION['error'] = "Username already exists";
            header("location: register.php");
        } else if ($userEmailExists) {  
            $_SESSION['error'] = "Email already exists";
            header("location: register.php");
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            try {

            $stmt = $pdo->prepare("INSERT INTO users(username, email, password) VALUES(?,?,?)");
            $stmt->execute([$username, $email, $hashedPassword]);
               $_SESSION['success'] = "Registration Successfully";
                header("location: login.php");
            

            } catch (PDOException $e) {
                $_SESSION['error'] = "Somthing went wrong, please try again";
                echo "Registration failed: " . $e->getMessage();
                header("locoation: register.php");
            

            }
        }
    }

    ?>