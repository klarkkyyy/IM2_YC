<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rentals - Construction Solution</title>
  <link rel="stylesheet" href="style.css" />
  
</head>
<body>
  <?php include 'navbar.php'; ?>

  <div class="rentals-main">
    <div class="rentals-content">
      <h1>Equipment Rentals Made Easy</h1>
      <p>Need reliable equipment for your next project? Yosech Construction offers a range of well-maintained, high-performance construction machinery available for rent.
        <br>Explore our available equipment below, complete with specifications and photos, and find the right tools to get the job done efficiently.
      </p>
      <div class="rentals-grid">
        <div class="rental-card available">
          <div class="status-label">
            <span class="dot green"></span> Available
          </div>
          <img src="backhoe-removebg-preview.png" alt="Backhoe">
          <div class="card-body">
            <h2 class="title backhoe">Backhoe</h2>
              <p>A versatile digging and loading machine with a front loader and rear excavator arm. Ideal for trenching, material handling, and small demolition tasks.</p>
              <p class="price">Daily: ₱6,000<br>Weekly: ₱36,000<br>Monthly: ₱130,000</p>
          </div>
          <div class="rent-button-wrapper">
                <a href="apply.php?equipment=Backhoe" class="rent-button">Rent Now</a>
          </div>
        </div>









        <div class="rental-card available">
          <div class="status-label">
            <span class="dot green"></span> Available
          </div>
          <img src="dumptruck.png" alt="Dumptruck">
          <div class="card-body">
            <h2 class="title dumptruck">Dumptruck</h2>
            <p>Used for transporting loose materials like sand, gravel, or demolition waste. Comes in 10-wheeler and 6-wheeler options.</p>
            <p class="price">Daily: ₱5,000<br>Weekly: ₱30,000<br>Monthly: ₱110,000</p>
          </div>
          <div class="rent-button-wrapper">
                <a href="apply.php?equipment=Backhoe" class="rent-button">Rent Now</a>
          </div>
        </div>

        <div class="rental-card unavailable">
          <div class="status-label">
            <span class="dot red"></span> Unavailable
          </div>
          <img src="roadroller.png" alt="Road Roller">
          <div class="card-body">
            <h2 class="title roadroller">Road Roller</h2>
            <p>Compacts soil, gravel, asphalt, and other materials to create a smooth, level surface. Commonly used in road construction.</p>
            <p class="price">Daily: ₱4,500<br>Weekly: ₱27,000<br>Monthly: ₱100,000</p>
          </div>
          <div class="rent-button-wrapper">
                <a href="apply.php?equipment=Backhoe" class="rent-button">Rent Now</a>
          </div>
        </div>

        <div class="rental-card available">
          <div class="status-label">
            <span class="dot green"></span> Available
          </div>
          <img src="transitmixer.jfif" alt="Transit Mixer">
          <div class="card-body">
            <h2 class="title transit">Transit Mixer</h2>
            <p>A truck used for transporting and mixing concrete. Keeps concrete in motion to prevent setting during transit.</p>
            <p class="price">Daily: ₱3,500<br>Weekly: ₱6,000<br>Monthly: ₱120,000</p>
          </div>
          <div class="rent-button-wrapper">
                <a href="apply.php?equipment=Backhoe" class="rent-button">Rent Now</a>
          </div>
        </div>

        <div class="rental-card available">
          <div class="status-label">
            <span class="dot green"></span> Available
          </div>
          <img src="cargotruck.jfif" alt="Cargo Truck">
          <div class="card-body">
            <h2 class="title cargo">Cargo Truck</h2>
            <p>Designed to transport goods, equipment, and supplies. Comes with either a closed van or open-bed configuration.</p>
            <p class="price">Daily: ₱4,000<br>Weekly: ₱25,000<br>Monthly: ₱90,000</p>
          </div>
          <div class="rent-button-wrapper">
                <a href="apply.php?equipment=Backhoe" class="rent-button">Rent Now</a>
          </div>
        </div>

        <div class="rental-card unavailable">
          <div class="status-label">
            <span class="dot red"></span> Unavailable
          </div>
          <img src="grader.png" alt="Grader">
          <div class="card-body">
            <h2 class="title grader">Grader</h2>
            <p>A long-blade machine used to level and smooth roads or land surfaces. Essential for road preparation and maintenance.</p>
            <p class="price">Daily: ₱6,000<br>Weekly: ₱36,000<br>Monthly: ₱130,000</p>
          </div>
          <div class="rent-button-wrapper">
                <a href="apply.php?equipment=Backhoe" class="rent-button">Rent Now</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
