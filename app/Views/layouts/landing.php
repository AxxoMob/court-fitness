<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Hitcourt — Tennis Platform for Players & Coaches</title>
        <!-- Bootstrap 5 CSS -->
        <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
        <!-- Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Google Fonts - using similar fonts to the image -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Oswald:wght@500;600;700&display=swap"
            rel="stylesheet">
        <link href="<?= base_url('assets/css/style.css?v='.APP_VERSION) ?>" rel="stylesheet">
        <!-- Slick carousel CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    </head>
    <body>
        <!-- Header & Hero -->
        <header class="hero-section">
            <!-- Hero background image slider -->
            <div id="hero-bg-slider">
                <div class="hero-bg-slide" style="background-image:url('assets/images/hero_img1.jpg')"></div>
                <div class="hero-bg-slide" style="background-image:url('assets/images/hero_img2.png')"></div>
                <div class="hero-bg-slide" style="background-image:url('assets/images/hero_img3.png')"></div>
                <div class="hero-bg-slide" style="background-image:url('assets/images/hero_img4.png')"></div>
            </div>
            <!-- <div class="hero-bg-overlay"></div> -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-transparent pb-4">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center" href="#">
                        <img src="assets/images/logo.png" alt="HitCourt Logo" height="34" class="me-2">
                        <!-- <span class="fw-bold">Hitcourt</span> -->
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                        <ul class="navbar-nav align-items-center d-none">
                            <li class="nav-item"> <a class="nav-link text-lime" href="#">Home</a> </li>
                            <li class="nav-item"> <a class="nav-link" href="#">Programs</a> </li>
                            <li class="nav-item"> <a class="nav-link" href="#">Training</a> </li>
                            <li class="nav-item"> <a class="nav-link" href="#">About Us</a> </li>
                            <li class="nav-item"> <a class="nav-link" href="#">Coaches</a> </li>
                            <li class="nav-item"> <a class="nav-link" href="#">Blog</a> </li>
                            <li class="nav-item"> <a class="nav-link me-3" href="#">Contact</a> </li>
                            <li class="nav-item">
                                <a href="#" class="btn btn-lime text-uppercase">Book Free Assessment</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="container-fluid ">
                <div class="container">
                    <div class="row align-items-start pt-5">
                        <div class="col-lg-6">
                            <div class="section-heading-label">Sports Performance Platform</div>
                            <h1 class="text-white text-uppercase">
                                TRAIN SMARTER.<br>
                                TRACK PROGRESS.<br>
                                <span class="text-lime">DOMINATE YOUR SPORT.</span>
                            </h1>
                            <p>
                                A platform built for athletes and coaches across all sports. Coaches assign sport-specific programs,
                                track
                                athlete fitness, monitor recovery, and measure performance — all in one place.
                            </p>
                            <div class="row mb-5">
                                <div class="col-md-4 feature-point mb-3 mb-md-0">
                                    <i class="fas fa-weight-hanging"></i>
                                    <div><b>Stronger Body</b><br>More Power</div>
                                </div>
                                <div class="col-md-4 feature-point mb-3 mb-md-0">
                                    <i class="fas fa-running"></i>
                                    <div><b>Faster Movement</b><br>Better Agility</div>
                                </div>
                                <div class="col-md-4 feature-point mb-3 mb-md-0">
                                    <i class="fas fa-heartbeat"></i>
                                    <div><b>Endless Stamina</b><br>Win More Games</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="https://www.org.hitcourt.com/login" class="btn btn-primary me-3 px-4 py-2">Start Your Journey <i class="fas fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-6 hero-image-col">
                            <!-- Background image of player is on the hero-section div -->
                            <!-- Or add a separate image element if preferred -->
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <main class="bg-off-white pb-0">
            <?= $this->renderSection('content') ?>
        </main>
        <!-- Footer -->
        <footer class="bg-dark-black py-3">
            <div class="container">
                <!-- Copyright -->
                <div class="copyright-bar text-center text-md-start p-0 m-0">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            &copy; <?= date('Y') ?> TennisFit. All rights reserved.
                        </div>
                        <div class="col-md-6 text-md-end footer-legal-links">
                            <a href="/" class="me-3">Privacy Policy</a>
                            <a href="/">Terms & Conditions</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- jQuery (required by Slick) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Slick carousel JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
        <!-- Bootstrap 5 JS Bundle with Popper -->
        <script src="<?= base_url('assets/js/bootstrap.bundle.min.min.js?v='.APP_VERSION) ?>"></script>
        <!-- Initialize Slick sliders -->
        <script>
          $(document).ready(function () {
            if ($.fn.slick) {
              // Hero background fade slider
              $('#hero-bg-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                fade: true,
                cssEase: 'ease-in-out',
                speed: 1200,
                autoplay: true,
                autoplaySpeed: 2500,
                arrows: false,
                dots: false,
                pauseOnHover: false,
                infinite: true
              });

              // Feature cards slider
              $('.feature-slider').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                arrows: true,
                dots: false,
                adaptiveHeight: true,
                responsive: [
                  { breakpoint: 992, settings: { slidesToShow: 2 } },
                  { breakpoint: 576, settings: { slidesToShow: 1 } }
                ]
              });

              // Program cards slider
              $('.program-slider').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                arrows: true,
                dots: false,
                adaptiveHeight: true,
                responsive: [
                  { breakpoint: 992, settings: { slidesToShow: 2 } },
                  { breakpoint: 576, settings: { slidesToShow: 1 } }
                ]
              });

            }
          });
        </script>
    </body>
</html>
