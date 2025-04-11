<?php
session_start();

// Check if the user is already authenticated
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {
    header('Location: mainpage.php');
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "UsTechComputers";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define maximum login attempts and initial lockout time
$maxAttempts = 5;
$initialLockoutTime = 60; // in seconds

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user is locked out
    if (isset($_SESSION['lockout']) && time() - $_SESSION['lockout'] < $_SESSION['lockoutTime']) {
        $lockoutRemaining = $_SESSION['lockoutTime'] - (time() - $_SESSION['lockout']);
        $errorMessage = "Account locked. Please try again in $lockoutRemaining seconds.";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Fetch user data from the database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the entered password against the stored hashed password
            if (password_verify($password, $user['password'])) {
                // Reset lockout counter on successful login
                unset($_SESSION['login_attempts']);
                unset($_SESSION['lockout']);
                unset($_SESSION['lockoutTime']);
                
                // Destroy any existing session and start a new one
                session_destroy();
                session_start();
                $_SESSION['check'] = true;

                // Redirect based on user role or username
                if ($user['username'] == 'admin') {
                    $_SESSION['admin'] = true;
                    header('Location: mainpage.php');
                } elseif ($user['username'] == 'user') {
                    $_SESSION['user'] = true;
                    header('Location: ustechcomputers.php');
                } else {
                    // Handle other roles or scenarios
                    $errorMessage = 'Invalid role';
                }

                exit;
            } else {
                $errorMessage = 'Invalid password';
            }
        } else {
            $errorMessage = 'User not found';
        }

        // Increase login attempts and set lockout if maximum attempts reached
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 1;
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= $maxAttempts) {
                if (isset($_SESSION['lockout'])) {
                    $_SESSION['lockoutTime'] *= 2; // Double the lockout time
                } else {
                    $_SESSION['lockoutTime'] = $initialLockoutTime;
                }
                $_SESSION['lockout'] = time();
                $errorMessage = "Account locked. Please try again in $_SESSION[lockoutTime] seconds.";
            }
        }

        $stmt->close();
        $conn->close();
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
         body {
            overflow-y:hidden;
            background-image: url("LoginBG.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;

                }
        img{
            position: relative;
            border: 14px inset #FFD700;
            border-radius: 10px;
            height: 170px;
            top: -20%;
            -webkit-animation: moveX 2s linear 0s infinite alternate, moveY 3.4s linear 0s infinite alternate;
            -moz-animation: moveX 2s linear 0s infinite alternate, moveY 3.4s linear 0s infinite alternate;
            -o-animation: moveX 2s linear 0s infinite alternate, moveY 3.4s linear 0s infinite alternate;
            animation: moveX 2s linear 0s infinite alternate, moveY 3.4s linear 0s infinite alternate;
        }

        @-webkit-keyframes moveX {
         from { left: 0; } to { left: 280px; }
        }
        @-moz-keyframes moveX {
         from { left: 0; } to { left: 280px; }
        }
        @-o-keyframes moveX {
         from { left: 0; } to { left: 280px; }
        }
        @keyframes moveX {
         from { left: 0; } to { left: 280px; }
        }

        @-webkit-keyframes moveY {
         from { top: 0; } to { top: 180px; }
        }
        @-moz-keyframes moveY {
         from { top: 0; } to { top: 180px; }
        }
        @-o-keyframes moveY {
         from { top: 0; } to { top: 180px; }
        }
        @keyframes moveY {
         from { top: 0; } to { top: 180px; }
        }
        
        .container{
            position:justify;
            
        }
        .login-container {
            margin-top: 42%;
            position: relative;
            width: 420px;
            padding: 20px;
            background-color: white;
            border: 14px ridge #FFD700;
            border-radius: 10px;
            -webkit-box-shadow: 4px 8px 25px 9px rgba(0,0,0,0.75); 
            box-shadow: 4px 8px 25px 9px rgba(0,0,0,0.5);
        }


        h2 {
            text-align: center;
            color: #333;
            font-size: 30px;
            font-family: 'Roboto', sans-serif;
            margin-bottom: 20px;
        }
        input {
            
            width: 93%;
                    padding: 15px;
                    margin-bottom: 10px;
                    border: none;
                    border-radius: 5px;
                    background-color: #ffd700;
                    font-size: 18px;
                    font-family: 'Roboto', sans-serif;
        }

        Button {
            width: 100%;
            color: rgb(255, 255, 255);
            font-size: 20px;
            line-height: 19px;
            padding: 10px;
            border-radius: 12px;
            font-family: 'Roboto', sans-serif;
            font-weight: normal;
            text-decoration: none;
            font-style: normal;
            font-variant: normal;
            text-transform: none;
            background: #ffd700;
            border: 2px solid rgb(0,0,0);
            display: inline-block;
            cursor: pointer;
        }
        Button:hover {
            background: #1C6EA4; 
        }

        Button:active {
            background: #144E75;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
        .marquee{

            margin-top: 12%;
        }
    </style>
        
</head>
<body>
    <audio>
<embed name="USTHYMN" src="ust.mp3"den="true" autostart="true">
<iframe src="ust.mp3" allow="autoplay" width="0px" height="0" frameborder="0" scrolling="auto"> </iframe>
    </audio>
    <div class="container">
    <div class="marquee">
        <img src="icon.png" alt="logo ng tamad" style="border-radius:50%; z-index:1;"> 
    </div>

    <div class="login-container">
        <h2>Welcome to USTECH!</h2>
        <form method="post" action="">
        <p id="lockout-message"></p>
<p id="lockout-timer"></p>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <?php if (isset($errorMessage)) : ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
     // Lockout countdown
     function updateLockoutTimer() {
        var lockoutTimerElement = document.getElementById("lockout-timer");
        var lockoutRemaining = <?php echo isset($lockoutRemaining) ? $lockoutRemaining : 0; ?>;
        
        if (lockoutRemaining > 0) {
            lockoutTimerElement.textContent = "Lockout remaining: " + lockoutRemaining + " seconds";
            setTimeout(function () {
                lockoutRemaining--;
                updateLockoutTimer();
            }, 1000);
        } else {
            lockoutTimerElement.textContent = "";
        }
    }

    updateLockoutTimer();


    function refreshPage() {
            location.reload(true);
        }

        setTimeout(refreshPage, 91000); 
</script>
        
  
</body>
</html>