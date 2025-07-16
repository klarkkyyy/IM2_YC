<?php
include 'database.php'; // make sure this file contains your DB connection ($conn)
?>

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
  <main>
  <div class="rentals-main">
    <div class="rentals-content">
      <h1>Equipment Rentals Made Easy</h1>
      <p>Need reliable equipment for your next project? Yosech Construction offers a range of well-maintained, high-performance construction machinery available for rent.
        <br>Explore our available equipment below, complete with specifications and photos, and find the right tools to get the job done efficiently.
      </p>

      <div class="rentals-grid">
        <?php
        $query = "SELECT * FROM equipment";
        $result = $conn->query($query);

        if ($result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
        ?>
            <div class="rental-card <?= strtolower($row['Availability']) ?>">
              <div class="status-label">
                <span class="dot <?= $row['Availability'] === 'Available' ? 'green' : 'red' ?>"></span>
                <?= $row['Availability'] ?>
              </div>
              <img src="<?= htmlspecialchars($row['ImagePath']) ?>" alt="<?= htmlspecialchars($row['EquipmentName']) ?>">
              <div class="card-body">
                <h2 class="title"><?= htmlspecialchars($row['EquipmentName']) ?></h2>
                <p><?= nl2br(htmlspecialchars($row['Description'])) ?></p>
                <p class="price">
                  Daily: ₱<?= number_format($row['DailyPrice']) ?><br>
                  Weekly: ₱<?= number_format($row['WeeklyPrice']) ?><br>
                  Monthly: ₱<?= number_format($row['MonthlyPrice']) ?>
                </p>
              </div>
              <div class="rent-button-wrapper">
                <a href="apply.php?equipment=<?= urlencode($row['EquipmentName']) ?>" class="rent-button">Rent Now</a>
              </div>
            </div>
        <?php
          endwhile;
        else:
          echo "<p>No equipment found.</p>";
        endif;
        ?>
      </div>
    </div>
  </div>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
