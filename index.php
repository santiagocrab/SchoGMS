<?php require_once __DIR__ . '/config/schogms_helpers.php'; ?>
<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scholarship and Grants Management System | SchoGMS</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logo.png">

    <!-- Bootstrap and Custom CSS -->
    <link href="dist/css/style.min.css" rel="stylesheet">

    <!--<link rel="manifest" href="manifest.json">-->
    
    <style>
        /* Background Blur Effect */
        .auth-wrapper {
            position: relative;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Add a pseudo-element for the background */
        .auth-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: url('') no-repeat center center;
            background-size: cover;
            filter: blur(10px);
            /* Adjust blur strength */
            z-index: -1;
            /* Keeps it behind the form */
        }

        /* Centered Login Box */
        .auth-box {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.9);
            /* Slight transparency */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        /* Error Message Styling */
        .error-message {
            background: #ffdddd;
            color: #a94442;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
<?php schogms_loading_screen_once(); ?>


    <div class="auth-wrapper">
        <div class="auth-box">
            <!-- Logo -->
           
            <div class="text-center mb-3">
                <img src="assets/images/image2.png" style="width: 90px;" alt="Homepage">
            </div>
            <div class="text-center mb-3">
                <img src="assets/images/logo.png" style="width: 300px;" alt="Homepage">
            </div>
            <p class="text-center text-dark">Enter your email (or username) and password to access.</p>

            <!-- Login Form -->
            <form method="post" action="login.php">
                <div class="form-group">
                    <label class="text-dark" for="uname">Email or username</label>
                    <input class="form-control" id="uname" type="text" name="username" placeholder="Enter your email or username"
                        required>
                </div>
                <div class="form-group">
                    <label class="text-dark" for="pwd">Password</label>
                    <input class="form-control" id="pwd" type="password" name="password"
                        placeholder="Enter your password" required>
                </div>

                <!-- Display Error Message if Login Fails -->
                <?php
                if (isset($_GET['ERROR'])) {
                    $error_message = '';

                    switch ($_GET['ERROR']) {
                        case 'restricted':
                            $error_message = 'Your account is restricted. Please contact support.';
                            break;
                        case 'session':
                            $error_message = 'Your session has expired. Please sign in again.';
                            break;
                        case 'pending':
                            $verifyEmail = htmlspecialchars(trim((string) ($_GET['email'] ?? '')), ENT_QUOTES, 'UTF-8');
                            $verifyHref = 'verify.php' . ($verifyEmail !== '' ? '?email=' . rawurlencode(trim((string) $_GET['email'])) : '');
                            $error_message = 'Your account is pending email verification. '
                                . '<a href="' . $verifyHref . '"><strong>Click here to enter your verification code.</strong></a>';
                            break;
                        case 'inactive':
                            $error_message = 'Your account is inactive. Please contact the administrator.';
                            break;
                        default:
                            $error_message = 'Error! Wrong username or password.';
                            break;
                    }

                    echo '<div class="alert alert-danger text-center">' . $error_message . '</div>';
                }
                ?>


                <button type="submit" class="btn btn-dark btn-block">Sign In</button>
            </form>
        </div>
    </div>

    <!-- Required JS -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>

    <script>
        $(".preloader").fadeOut();
    </script>
     
    <footer>
        
    <script>
//         if ("serviceWorker" in navigator) {
//   navigator.serviceWorker
//     .register("/pwabuilder-sw.js")
//     .then((registration) => {
//       console.log("Service Worker registered with scope:", registration.scope);
//     })
//     .catch((error) => {
//       console.log("Service Worker registration failed:", error);
//     });
// }
    </script>
     <script>
if(navigator.serviceWorker) { // Ensure this path is correct
    navigator.serviceWorker.register('serviceWorker-sw.js') 
}
</script>
    </footer>
</body>

</html>