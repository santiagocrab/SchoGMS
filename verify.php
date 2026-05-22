<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scholarship and Grants Management System | SchoGMS</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logo.png">

    <!-- Bootstrap, Custom CSS, and SweetAlert -->
    <link href="dist/css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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

        .auth-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('assets/images/image2.png') no-repeat center center;
            background-size: cover;
            filter: blur(10px);
            z-index: -1;
        }

        /* Centered Login Box */
        .auth-box {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">
        <div class="auth-box">
            <!-- Logo -->
            <div class="text-center mb-3">
                <img src="assets/images/logo.png" style="width: 300px;" alt="Homepage">
            </div>
            <p class="text-center text-dark">Enter your email and the 6-digit verification code from your welcome email.</p>
            <p class="text-center text-muted small">Registrar, coordinator, and director accounts must verify before login.</p>

            <!-- Login Form -->
            <form id="verifyForm">
                <div class="form-group">
                    <label class="text-dark" for="user_email">Email</label>
                    <input class="form-control" id="user_email" type="email" name="user_email" placeholder="Enter your email" required
                        value="<?= htmlspecialchars(trim((string) ($_GET['email'] ?? '')), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label class="text-dark" for="verification_code">Verification Code</label>
                    <input class="form-control" id="verification_code" type="text" name="verification_code" placeholder="Enter your verification code" required>
                </div>
                <button type="submit" class="btn btn-dark btn-block">Verify</button>
            </form>
        </div>
    </div>

    <!-- Required JS -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $("#verifyForm").on("submit", function (e) {
                e.preventDefault();

                let email = $("#user_email").val();
                let code = $("#verification_code").val();

                $.ajax({
                    url: "verify_code.php",
                    type: "POST",
                    data: { user_email: email, verification_code: code },
                    dataType: "json",
                    success: function (response) {
                        if (response.success && response.redirect) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Verified!',
                                text: 'Redirecting to your dashboard…',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        } else if (response.already_verified && response.redirect) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Already verified',
                                text: response.error || 'You can log in now.'
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Verification Failed',
                                text: response.error || 'Invalid or expired verification code.'
                            });
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Something went wrong. Please try again.';
                        if (xhr.responseText) {
                            try {
                                const j = JSON.parse(xhr.responseText);
                                if (j.error) msg = j.error;
                            } catch (e) {
                                if (xhr.responseText.length < 300) msg = xhr.responseText;
                            }
                        }
                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    }
                });
            });
        });
    </script>

</body>
</html>
