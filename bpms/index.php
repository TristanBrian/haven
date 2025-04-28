<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

echo "<!-- Debug: Script started -->\n";

try {
    echo "<!-- Debug: Before dbconnection include -->\n";
    include('includes/dbconnection.php');
    echo "<!-- Debug: After dbconnection include -->\n";
    
    // Test database connection
    if ($con) {
        echo "<!-- Debug: Database connected successfully -->\n";
    } else {
        echo "<!-- Debug: Database connection failed -->\n";
    }
} catch (Exception $e) {
    die("<!-- Database connection error: " . htmlspecialchars($e->getMessage()) . " -->");
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>SPA| Home Page</title>
    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
</head>
<body id="home">

<?php include_once('includes/header.php'); ?>

<script>
(function() {
    if (!window.chatbase || window.chatbase("getState") !== "initialized") {
        window.chatbase = (...arguments) => {
            if (!window.chatbase.q) {
                window.chatbase.q = [];
            }
            window.chatbase.q.push(arguments);
        };
        window.chatbase = new Proxy(window.chatbase, {
            get(target, prop) {
                if (prop === "q") {
                    return target.q;
                }
                return (...args) => target(prop, ...args);
            }
        });
    }
    const onLoad = function() {
        const script = document.createElement("script");
        script.src = "https://www.chatbase.co/embed.min.js";
        script.id = "vuxPPe1ejQW5zsy2Cio-A";
        script.domain = "www.chatbase.co";
        document.body.appendChild(script);
    };
    if (document.readyState === "complete") {
        onLoad();
    } else {
        window.addEventListener("load", onLoad);
    }
})();
</script>

<script src="assets/js/jquery-3.3.1.min.js"></script> <!-- Common jquery plugin -->
<script src="assets/js/bootstrap.min.js"></script> <!-- Bootstrap working -->
<script>
$(function () {
    $('.navbar-toggler').click(function () {
        $('body').toggleClass('noscroll');
    });
});
</script>

<div class="w3l-hero-headers-9">
    <div class="css-slider">
        <input id="slide-1" type="radio" name="slides" checked>
        <section class="slide slide-one">
            <div class="container">
                <div class="banner-text">
                    <h4>Creative Styling</h4>
                    <h3>Haven Spa<br>Get the ultimate beauty and spa services</h3>
                    <a href="book-appointment.php" class="btn logo-button top-margin">Get An Appointment</a>
                </div>
            </div>
        </section>
        <input id="slide-2" type="radio" name="slides">
        <section class="slide slide-two">
            <div class="container">
                <div class="banner-text">
                    <h4>Creative Styling</h4>
                    <h3>Haven Spa<br>Get all your spa and beauty needs</h3>
                    <a href="book-appointment.php" class="btn logo-button top-margin">Get An Appointment</a>
                </div>
            </div>
        </section>
        <header>
            <label for="slide-1" id="slide-1"></label>
            <label for="slide-2" id="slide-2"></label>
        </header>
    </div>
</div>

<section class="w3l-call-to-action_9">
    <div class="call-w3 ">
        <div class="container">
            <div class="grids">
                <div class="grids-content row">
                    <div class="column col-lg-4 col-md-6 color-2 ">
                        <div>
                            <h4 class=" ">Our Salon is Most Popular</h4>
                            <p class="para ">Eline Hair and Beauty Salon Offers - Beauty Services</p>
                            <a href="about.php" class="action-button btn mt-md-4 mt-3">Read more</a>
                        </div>
                    </div>
                    <div class="column col-lg-4 col-md-6 col-sm-6 back-image  ">
                        <img src="assets/images/5.jpg" alt="product" class="img-responsive ">
                    </div>
                    <div class="column col-lg-4 col-md-6 col-sm-6 back-image2 ">
                        <img src="assets/images/6.jpg" alt="product" class="img-responsive ">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="w3l-teams-15">
    <div class="team-single-main ">
        <div class="container">
            <div class="column2 image-text">
                <h3 class="team-head ">Come experience the secrets of relaxation</h3>
                <p class="para  text ">
                    Best Beauty expert at your home and provides beauty salon at home. Home Salon provide well trained beauty professionals for beauty services at home including Facial, Clean Up, Bleach, Waxing, Pedicure, Manicure, etc.
                </p>
                <a href="book-appointment.php" class="btn logo-button top-margin mt-4">Get An Appointment</a>
            </div>
        </div>
    </div>
</section>

<script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
<div class="elfsight-app-1b8d3220-9ea5-43e7-8ebd-60bf7b31e20f" data-elfsight-app-lazy></div>

<?php include_once('includes/footer.php'); ?>

<!-- move top -->
<button onclick="topFunction()" id="movetop" title="Go to top">
    <span class="fa fa-long-arrow-up"></span>
</button>
<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function () {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("movetop").style.display = "block";
        } else {
            document.getElementById("movetop").style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
</script>
<!-- /move top -->
</body>
</html>
