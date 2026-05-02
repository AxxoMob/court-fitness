<?= $this->extend('layouts/landing') ?>

<?= $this->section('content') ?>
<!-- Feature Icon Cards -->
<section class="container feature-cards-wrapper">
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="card feature-icon-card">
                <i class="fas fa-dumbbell"></i>
                <h5>Sport-Specific Workouts</h5>
                <p>Programs designed for your sport, position and goals.</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card feature-icon-card">
                <i class="fas fa-shield-alt"></i>
                <h5>Injury Prevention</h5>
                <p>Reduce risk and stay on court longer.</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card feature-icon-card">
                <i class="fas fa-chart-line"></i>
                <h5>Performance Tracking</h5>
                <p>Monitor progress and see real results.</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card feature-icon-card">
                <i class="fas fa-user-tie"></i>
                <h5>Expert Coaches</h5>
                <p>Learn from certified tennis fitness experts.</p>
            </div>
        </div>
    </div>
</section>
<!-- Our Programs Section -->
<section class="container py-5 mt-5">
    <div class="text-center mb-5">
        <div class="section-heading-label">Our Programs</div>
        <h2 class="fw-bold">Fitness Programs Built for Every Athlete</h2>
        <p class="mx-auto" style="max-width: 600px;">Complete fitness solutions to help athletes across all sports
            reach their peak.
        </p>
    </div>
    <div class="position-relative px-5">
        <div class="program-slider">
            <!-- Program Card 1 -->
            <div class="program-slide">
                <div class="program-card shadow">
                    <img src="assets/images/strength_training.png" class="card-img-top" alt="Strength Training">
                    <div class="program-card-body">
                        <i class="fas fa-dumbbell"></i>
                        <h4>Strength Training</h4>
                        <p>Build explosive power, core stability and overall strength for any sport.</p>
                        <a href="#" class="learn-more-link">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <!-- Program Card 2 -->
            <div class="program-slide">
                <div class="program-card shadow">
                    <img src="assets/images/speed_agility.png" class="card-img-top" alt="Speed & Agility">
                    <div class="program-card-body">
                        <i class="fas fa-bolt text-lime"></i>
                        <h4>Speed & Agility</h4>
                        <p>Improve footwork, reaction time and movement to outplay opponents in any sport.</p>
                        <a href="#" class="learn-more-link">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <!-- Program Card 3 -->
            <div class="program-slide">
                <div class="program-card shadow">
                    <img src="assets/images/endurance_stamina.png" class="card-img-top" alt="Endurance & Stamina">
                    <div class="program-card-body">
                        <i class="fas fa-heartbeat text-lime"></i>
                        <h4>Endurance & Stamina</h4>
                        <p>Increase game endurance and stay strong from the opening whistle to the final buzzer.</p>
                        <a href="#" class="learn-more-link">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <!-- Program Card 4 -->
            <div class="program-slide">
                <div class="program-card shadow">
                    <img src="assets/images/mobility_recovery.png" class="card-img-top" alt="Mobility & Recovery">
                    <div class="program-card-body">
                        <i class="fas fa-shield-alt text-lime"></i>
                        <h4>Mobility & Recovery</h4>
                        <p>Enhance flexibility, prevent injuries and recover faster for peak performance.</p>
                        <a href="#" class="learn-more-link">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <!-- Program Card 1 -->
            <div class="program-slide">
                <div class="program-card shadow">
                    <img src="assets/images/strength_training.png" class="card-img-top" alt="Strength Training">
                    <div class="program-card-body">
                        <i class="fas fa-dumbbell"></i>
                        <h4>Strength Training</h4>
                        <p>Build explosive power, core stability and overall strength for any sport.</p>
                        <a href="#" class="learn-more-link">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Why Choose Us / Counters Section (Dark) -->
<section class="counter-section bg-dark-black text-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 more-than-text-wrapper">
                <div class="section-heading-label">Why Choose Us</div>
                <h2 class="text-white fw-bold mb-3">More Than Just a Workout</h2>
                <p>We combine science, experience and passion to bring out the best in every athlete, regardless of sport.
                </p>
                <ul class="features-list mb-4">
                    <li><i class="far fa-check-circle"></i> Sport-specific fitness programs</li>
                    <li><i class="far fa-check-circle"></i> Certified & experienced coaches</li>
                    <li><i class="far fa-check-circle"></i> Personalized training plans</li>
                    <li><i class="far fa-check-circle"></i> Injury prevention focus</li>
                    <li><i class="far fa-check-circle"></i> Track progress & achieve goals</li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- Testimonials Section -->
<section class="bg-off-white py-5">
    <!-- Transform Your Game Banner -->
    <div class="container">
        <div class="pre-footer-banner">
            <div class="row align-items-center g-4 px-lg-5">
                <div class="col-lg-12 text-center text-lg-start">
                    <h2 class="fw-bold mb-2 text-white">Ready to Transform Your Game?</h2>
                    <p class="text-white mb-0" style="opacity:0.8; font-size: 0.85rem;">
                        Join our sports performance platform and start your journey towards becoming a stronger, faster,
                        better athlete — whatever your sport.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
