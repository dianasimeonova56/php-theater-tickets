<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['user_role'] == 'admin') {
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
    <link rel="stylesheet" href="../assets/css/jquery.seat-charts.css">
    <script src="../assets/css/styles.css"></script>
    <script src="../assets/js/main.js"></script>
    <style>
        .regular {
            color: white;
            background-color: #43aa8b;
        }
    </style>
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
                    if (isset($_SESSION['username'])) {
                        echo '
                                <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="/pages/load_plays.php">Plays</a></li>';
                        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
                            echo '<li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="/pages/add_play.php">Add a play</a></li>';
                        } else {
                            echo '<li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="/pages/book_play.php">Book a play</a></li>
                                ';
                        }
                        echo '<li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="/pages/my_profile.php">My Profile</a></li>';
                        echo '<li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" id="logout" href="#">Logout</a></li>';
                    } else {
                        echo '
                                <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="/pages/register_page.php">Register</a></li>
                                <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded" href="/pages/login_page.php">Login</a></li>';
                    };
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <section class="page-section " id="bookPlay">
        <div class="container pt-5">
            <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">Book play</h2>
            <div class="divider-custom">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fa-solid fa-ticket"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <form id="bookPlayForm">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="form-floating mb-3">
                            <select class="form-control" id="playName" name="play">
                                <option selected>Select a play</option>
                            </select>
                            <label for="name">Play Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <div id="seat-map" class="d-none"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-xl-6 d-none" id="booking-details">
                        <h4>Booking Details</h4>
                        <p>Selected Seats (<span id="selected-count">0</span>): <span id="selected-seats"></span></p>
                        <p>Total: $<span id="total-price">0</span></p>
                        <div id="legend"></div>
                        <button class="btn btn-primary btn-xl" id="bookPlayBtn" type="submit" disabled>Book Play</button>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="../assets/js/jquery.seat-charts.min.js"></script>

</body>

</html>