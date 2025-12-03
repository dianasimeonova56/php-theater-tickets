<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Theater App</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
    <link href="../assets/css/styles.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="../assets/js/main.js"></script>
</head>

<body id="page-top">
    <nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Theater Tickets</a>
            <button class="navbar-toggler text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto">
                    <?php
                    echo '
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="/kursowa/pages/register_page.php">Register</a></li>
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="/kursowa/pages/login_page.php">Login</a></li>';
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <section class="page-section" id="register">
        <div class="container pt-5">
            <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">Register</h2>
            <div class="divider-custom">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fa-solid fa-ticket"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <form id="registerForm">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="first_name" name="first_name" type="text" placeholder="Enter your name..." required />
                            <label for="name">First Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="last_name" name="last_name" type="text" placeholder="Enter your name..." required />
                            <label for="name">Last Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="username" name="username" type="text" placeholder="Enter your name..." required />
                            <label for="name">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="email" type="email" name="email" placeholder="name@example.com" required />
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="phone" type="tel" name="phone" placeholder="(123) 456-7890" required />
                            <label for="phone">Phone number</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="password" type="password" name="password" required />
                            <label for="password">Password</label>
                            <p class="text-muted">Password should be at least 8 characters long, contain 1 uppercase, 1 lowercase, 1 number!</p>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="repeat_password" type="password" name="repeat_password" required />
                            <label for="repeat-password">Repeat Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <div class="g-recaptcha" data-sitekey="6LcggA4sAAAAALnc21rFMHh3ZcGe-htD_heRtrfF"></div>
                        </div>
                        <button class="btn btn-primary btn-xl" id="registerBtn" type="submit">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="../assets/css/styles.css"></script>
</body>

</html>