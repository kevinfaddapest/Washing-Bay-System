<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AUTO Detail Car Wash Management</title>
  <link rel="stylesheet" href="style.css">

  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="assets/slick/slick.css">
  <link rel="stylesheet" href="assets/slick/slick-theme.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
<style>
.body { font-family: "Poppins", sans-serif; margin:0; background:#f5f7fa; color:#333; }
  header { background:#004aad; padding:20px; text-align:center; color:white; }
  header h1 { margin:0; font-size:2em; }
.hero { background:url("assets/images/car wash9.jpeg") center/cover no-repeat; height:80vh; display:flex; flex-direction:column; justify-content:center; align-items:center; color:white; text-align:center; position:relative; }
.hero::after { content:""; position:absolute; inset:0; background:rgba(0,0,0,0.4); }
.hero * { position:relative; z-index:1; }
.hero h2 { font-size:2.5em; }
.hero p { font-size:1.2em; }
.hero a { padding:12px 25px; background:#007BFF; color:white; text-decoration:none; border-radius:30px; margin-top:10px; }
.login-card { background:white; color:#333; padding:25px; border-radius:10px; width:280px; margin-top:20px; box-shadow:0 0 20px rgba(0,0,0,0.3); }
.login-card a { display:block; margin-top:10px; padding:10px; background:#007BFF; color:white; border-radius:5px; text-decoration:none; }
.welcome { text-align:center; padding:50px 20px; }
.welcome img { width:150px; border-radius:10px; }
.slider { width:85%; margin:40px auto; }
.slider img { width:100%; height:600px; object-fit:cover; border-radius:10px; }
.info { text-align:center; background:white; width:85%; margin:40px auto; padding:50px; border-radius:10px; }
.info-card { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; }
.card { flex:1 1 200px; padding:25px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,.1); }
.card h3 { color:#004aad; }
.card p { font-size:24px; font-weight:700; color:#007BFF; }
.features { display:flex; flex-wrap:wrap; justify-content:center; gap:30px; background:#eaf3ff; padding:50px; }
.feature { background:white; width:250px; padding:20px; text-align:center; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,.1); }
.testimonials { width:80%; margin:50px auto; text-align:center; }
.testimonial { background:white; padding:30px; border-radius:10px; margin:10px; box-shadow:0 0 15px rgba(0,0,0,.1); }
.contact { background:#425879FF; color:white; text-align:center; padding:50px; }
footer { background:#007BFF; color:white; text-align:center; padding:15px; }
@media(max-width:768px){ .features,.info-card{flex-direction:column;} }
.marquee-container {
  overflow: hidden;
  white-space: nowrap;
  width: 100%;
  background: #000; /* optional */
  color: #fff;     /* optional */
}

.marquee-text {
  display: inline-block;
  padding-left: 100%;
  animation: scroll-text 15s linear infinite;
}

@keyframes scroll-text {
  0% {
    transform: translateX(0%);
  }
  100% {
    transform: translateX(-100%);
  }
}

/* Make it smoother on small screens */
@media (max-width: 768px) {
  .marquee-text {
    animation-duration: 10s;
  }
}

  </style>
</head>

<body>

 <header>
  <h1>🚗 AUTO Detail Car Wash</h1>
  <nav>
    <a href="dashboard.php">
      <div class="marquee-container">
        <div class="marquee-text">
          AUTO Detail Car Wash - Your Professional Car Wash Bay You Can Trust
        </div>
      </div>
    </a>
  </nav>
</header>
  <section class="hero">
    <h2>Professional Car Care You Can Trust</h2>
    <p>Fast • Affordable • Spotless Shine Every Time</p>
    <div class="login-card">
    <h3>Access Your Account</h3>
    <p>Click below to log in to the system.</p>
    <a href="login.php">Login Here!</a>
    </div>
    <a href="#services">Explore Services</a>
  </section>

  <main>
    <section class="welcome">
      <h2>Welcome to AUTO Detail Car Wash Management System</h2>
      <p>Manage, track, and organize your car wash operations effortlessly.</p>
      <img src="assets/images/car wash6.jpeg" alt="Car Wash Logo" class="logo">
    </section>

    <!-- Slider -->
    <section class="slider">
      <div><img src="assets/images/car wash4.jpg" alt="Car Wash 1"></div>
      <div><img src="assets/images/car wash2.jpg" alt="Car Wash 2"></div>
      <div><img src="assets/images/car wash3.jpg" alt="Car Wash 3"></div>
      <div><img src="assets/images/car wash4.jpg" alt="Car Wash 4"></div>
      <div><img src="assets/images/car wash5.jpg" alt="Car Wash 5"></div>
      <div><img src="assets/images/car wash7.jpg" alt="Car Wash 7"></div>
      <div><img src="assets/images/car wash8.jpg" alt="Car Wash 8"></div>
    </section>

    <section id="services" class="info">
      <h2>Our Services</h2>
      <p>We offer high-quality car wash services including full detailing, waxing, vacuuming, and engine cleaning. Let us make your car shine like new!</p>
    </section>

    <section class="features">
  <div class="feature">
    <i class="fa-solid fa-bolt fa-3x" style="color:#007BFF;"></i>
    <h3>Quick Service</h3>
    <p>Get your car sparkling clean in no time!</p>
  </div>

  <div class="feature">
    <i class="fa-solid fa-leaf fa-3x" style="color:#28a745;"></i>
    <h3>Eco Friendly</h3>
    <p>We use biodegradable products that are safe for your car and the environment.</p>
  </div>

  <div class="feature">
    <i class="fa-solid fa-star fa-3x" style="color:#ffc107;"></i>
    <h3>Quality Guaranteed</h3>
    <p>Your satisfaction is our top priority.</p>
  </div>
</section>


    <section class="testimonials">
      <h2>What Our Customers Say</h2>
      <div class="slider testimonials-slider">
        <div class="testimonial">
          <p>“Fantastic service! My car has never looked this clean before.”</p>
          <h4>Mr. Mulabbi.</h4>
        </div>
        <div class="testimonial">
          <p>“Affordable, fast, and friendly. Highly recommend!”</p>
          <h4>Mr. Kato.</h4>
        </div>
        <div class="testimonial">
          <p>“Great experience. The detailing work was excellent.”</p>
          <h4>Mr. Derrick.</h4>
        </div>
      </div>
    </section>

    <section class="contact">
  <h2>Contact Us</h2>
  <p>📍 Market Sheet, Entebbe, Uganda</p>
  <p>📞 +256 (703) 414-971, +256 (700) 667-769</p>
  <p style="color:yellow;">📧 <a href="mailto:makangaautocentre@gmail.com" style="color:yellow;">makangaautocentre@gmail.com</a></p>
</section>

  </main>

  <footer>
    <p>© <?= date('Y'); ?> AUTO Detail Car Wash | Designed for simplicity & performance 🚘</p>
  </footer>

  <!-- jQuery & Slick JS -->
  <script src="assets/jquery/jquery-3.7.0.min.js"></script>
<script src="assets/slick/slick.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
  $(document).ready(function () {
    $('.slider').slick({
      autoplay: true,
      dots: true,
      arrows: true
    });

    $('.testimonials-slider').slick({
      autoplay: true,
      dots: true,
      arrows: false
    });
  });
</script>
</body>
</html>
