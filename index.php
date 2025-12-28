<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
   <link rel="stylesheet" href="assets/css/style.css?v=1.1">
</head>
<body>

<?php include "includes/header.php"; ?>

<!-- HERO GRID -->
<section class="hero-grid">

  <div class="hero-card bg-1 main-hero">
    <img src="assets/images/t.jpg" alt="Hero 1">
    <div class="hero-text">
      <h1>Welcome to<br>MyBlog.</h1>
    </div>
  </div>

  <div class="hero-card bg-2">
    <img src="assets/images/tt.jpg" alt="Hero 2">
    <div class="hero-text">
      <h2>Keep<br>Writing</h2>
    </div>
  </div>

  <div class="hero-card bg-3">
    <img src="assets/images/ttt.jpg" alt="Hero 3">
    <div class="hero-text">
      <h3>Keep<br>Blooming</h3>
    </div>
  </div>

</section>


<!-- PURPOSE -->
<section class="purpose">
  <h2>What is MyBlog?</h2>
  <p>
    MyBlog is a personal blogging platform created to share thoughts,
    experiences, and knowledge about web development, design, and creativity.
    It is a space where ideas grow through writing.
  </p>
</section>

<!-- IMAGES -->
<section class="about-images">
  <div class="about-image">
    <img src="assets/images/o.jpg" alt="">
    <p>Writing ideas and thoughts</p>
  </div>

  <div class="about-image">
    <img src="assets/images/oo.jpg" alt="">
    <p>A calm space to create</p>
  </div>

  <div class="about-image">
    <img src="assets/images/o3.jpg" alt="">
    <p>Minimal and meaningful design</p>
  </div>
</section>

<!-- WHY BLOG -->
<section class="why-blog">
  <h2>Why Should People Write Blogs?</h2>

  <div class="why-grid">
    <div class="why-item">
      <h3>✦ Express Yourself</h3>
      <p>Blogging helps people express their thoughts and ideas freely.</p>
    </div>

    <div class="why-item">
      <h3>✦ Learn by Writing</h3>
      <p>Writing strengthens understanding and improves learning.</p>
    </div>

    <div class="why-item">
      <h3>✦ Build a Digital Identity</h3>
      <p>Blogs create a personal archive and online presence.</p>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials">
  <h2>What Readers Say</h2>

  <div class="testimonial-track">
    <div class="testimonial">“A clean and inspiring blog design.”</div>
    <div class="testimonial">“Makes writing feel meaningful.”</div>
    <div class="testimonial">“Simple, modern and thoughtful.”</div>
    <div class="testimonial">“A calm space for creative minds.”</div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-content">
    <p><strong>MyBlog.</strong></p>
    <p>Email: myblog@email.com</p>
    <p>Instagram: @myblog</p>
    <p>© 2025 MyBlog</p>
  </div>
</footer>
<script src="/assets/js/main.js"></script>
</body>


</html>
