<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'client') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Client Dashboard</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include 'navbar.php'; ?>

  <section class="hero">
    <h1 class="index">Welcome to Yosech Construction</h1>
    <p class="index">
      Yosech Construction is a trusted construction firm based in Dipolog City, delivering high-quality road infrastructure, residential and commercial buildings, flood control projects, and reliable equipment rentals. With decades of experience, we blend traditional craftsmanship with modern techniques to create structures that stand the test of time while embracing sustainability and cultural authenticity.
    </p>
    <a href="rentals.php" class="rentals-button">Browse Rentals</a>
  </section>

  <section class="content">
    <div class="content-left">
      <div class="blue-bg">
        <h2>147</h2>
        <p>Completed Projects</p>
      </div>
      <div class="red-bg">
        <h2>25+</h2>
        <p>Years of Experience</p>
      </div>
    </div>
    <div class="content-right">
      <div class="white-box">
        <h2>We Construct and Manage Places and Infrastructures</h2>

        <div class="service">
          <img src="Shape.png" alt="Road Construction & Infrastructure Projects" />
          <div>
            <h3>Road Construction & Infrastructure Projects</h3>
            <p>We build durable roads and vital infrastructure that connect communities.</p>
          </div>
        </div>

        <div class="service">
          <img src="Shape2.png" alt="Residential & Commercial Building Construction" />
          <div>
            <h3>Residential & Commercial Building Construction</h3>
            <p>From homes to businesses, we create safe and modern spaces for living and working.</p>
          </div>
        </div>

        <div class="service">
          <img src="Shape3.png" alt="Flood Control & Drainage Solutions" />
          <div>
            <h3>Flood Control & Drainage Solutions</h3>
            <p>Our projects protect communities with reliable flood control and drainage systems.</p>
          </div>
        </div>

        <div class="service">
          <img src="Shape2.png" alt="Heavy Equipment Rentals" />
          <div>
            <h3>Heavy Equipment Rentals</h3>
            <p>Rent high-quality construction equipment for your projects with ease and confidence.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="additional-content">
    <div>
      <h3 class="highlighted-title" style="text-align: center;">Construction</h3>
      <p class="white-paragraph">
        Our project managers and superintendents are very experienced in completing highly specialized, multi-faceted projects. Their ability to foresee and therefore prevent problems is invaluable. Construction mastery aids in the design process of the CM/GC and Design-Build delivery methods because we know what is feasible and have a large repertoire of means and methods. Yosech Construction is uniquely qualified in tilt-up concrete construction. We know what is feasible and we have the means and methods to accomplish the goal.
      </p>
    </div>
    <div>
      <h3 class="highlighted-title" style="text-align: center;">Engineering</h3>
      <p class="white-paragraph">
        Our engineers and designers are qualified to solve the challenges presented in today's design and construction market. We have experience in a variety of projects and delivery methods. We know what is feasible and we have the means and methods to accomplish the goal.
      </p>
    </div>
    <div>
      <h3 class="highlighted-title" style="text-align: center;">Innovation</h3>
      <p class="white-paragraph">
        We are committed to continually learning and improving. This pursuit of knowledge drives our team to innovative solutions. We are innovative and will draw on all of our resources to find solutions to meet owner needs, even if that means inventing something new.
      </p>
    </div>
  </section>

  <?php include 'footer.php'; ?>
</body>
</html>
