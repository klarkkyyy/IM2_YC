<?php
session_start();

require 'navbar.php';

if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Client') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Get client's projects and updates
$client_id = $_SESSION['User_id'];
$projects_query = "SELECT p.* FROM projects p WHERE p.client_id = ?";
$stmt = mysqli_prepare($conn, $projects_query);
mysqli_stmt_bind_param($stmt, "i", $client_id);
mysqli_stmt_execute($stmt);
$projects_result = mysqli_stmt_get_result($stmt);
$projects = mysqli_fetch_all($projects_result, MYSQLI_ASSOC);

// Count unread updates
$unread_query = "SELECT COUNT(*) FROM project_updates u 
                JOIN projects p ON u.project_id = p.project_id
                WHERE p.client_id = ? AND u.is_read = 0";
$stmt = mysqli_prepare($conn, $unread_query);
mysqli_stmt_bind_param($stmt, "i", $client_id);
mysqli_stmt_execute($stmt);
$unread_result = mysqli_stmt_get_result($stmt);
$unread_count = mysqli_fetch_row($unread_result)[0];

// Get latest updates for each project
$project_updates = [];
foreach ($projects as $project) {
    $update_query = "SELECT * FROM project_updates 
                    WHERE project_id = ? 
                    ORDER BY update_date DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "i", $project['project_id']);
    mysqli_stmt_execute($stmt);
    $update_result = mysqli_stmt_get_result($stmt);
    $project_updates[$project['project_id']] = mysqli_fetch_assoc($update_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Client Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Notification styles */
    .notification-badge {
      position: relative;
      display: inline-block;
    }
    
    .notification-count {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #ff4757;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      font-size: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .notification-dropdown {
      position: absolute;
      right: 0;
      top: 100%;
      background: white;
      width: 350px;
      max-height: 400px;
      overflow-y: auto;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-radius: 5px;
      z-index: 1000;
      display: none;
    }
    
    .notification-dropdown.show {
      display: block;
    }
    
    .notification-item {
      padding: 12px 15px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
    }
    
    .notification-item.unread {
      background: #f8f9fa;
    }
    
    .notification-item:hover {
      background: #f1f2f6;
    }
    
    .notification-header {
      display: flex;
      justify-content: space-between;
      padding: 10px 15px;
      border-bottom: 1px solid #eee;
      background: #004AAD;
      color: white;
    }
    
    .mark-all-read {
      color: white;
      text-decoration: underline;
      cursor: pointer;
    }

    /* Project status cards */
    .projects-overview {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 20px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .projects-overview h2 {
      color: #004AAD;
      margin-top: 0;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }

    .project-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .project-card {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      border-left: 4px solid #004AAD;
      transition: transform 0.3s ease;
    }

    .project-card:hover {
      transform: translateY(-5px);
    }

    .project-card h3 {
      margin-top: 0;
      color: #004AAD;
    }

    .project-status {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 10px 0;
    }

    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: bold;
    }

    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }

    .status-in-progress {
      background-color: #cce5ff;
      color: #004085;
    }

    .status-completed {
      background-color: #d4edda;
      color: #155724;
    }

    .progress-container {
      margin: 15px 0;
    }

    .progress-text {
      font-size: 0.9rem;
      margin-bottom: 5px;
      display: flex;
      justify-content: space-between;
    }

    .progress-bar {
      height: 8px;
      background: #e0e0e0;
      border-radius: 4px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: #004AAD;
      transition: width 0.5s ease;
    }

    .last-update {
      font-size: 0.8rem;
      color: #666;
      margin-top: 10px;
    }

    .view-updates {
      display: inline-block;
      margin-top: 15px;
      padding: 8px 15px;
      background-color: #004AAD;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      font-size: 0.9rem;
      transition: background-color 0.3s;
    }

    .view-updates:hover {
      background-color: #003080;
    }

    .no-projects {
      text-align: center;
      padding: 30px;
      color: #666;
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="navbar-center">
      <a href="Index.php">
        <img src="logo.png" alt="Logo" class="logo-centered">
      </a>
      <div class="nav-links">
        <a href="Projects.php">Projects</a>
        <a href="rentals.php">Rentals</a>
        <a href="Apply.php">Apply</a>
      </div>
    </div>
    <div class="navbar-right">
      <div class="nav-links">
        <a href="Contact.php">Contacts</a>
        <div class="notification-badge">
          <a href="#" id="notification-toggle">Updates</a>
          <?php if ($unread_count > 0): ?>
            <span class="notification-count"><?= $unread_count ?></span>
          <?php endif; ?>
          <div class="notification-dropdown" id="notification-dropdown">
            <div class="notification-header">
              <span>Project Updates</span>
              <span class="mark-all-read">Mark all as read</span>
            </div>
            <?php 
            $updates_query = "SELECT u.*, p.project_name 
                            FROM project_updates u
                            JOIN projects p ON u.project_id = p.project_id
                            WHERE p.client_id = ?
                            ORDER BY u.update_date DESC LIMIT 5";
            $stmt = mysqli_prepare($conn, $updates_query);
            mysqli_stmt_bind_param($stmt, "i", $client_id);
            mysqli_stmt_execute($stmt);
            $updates_result = mysqli_stmt_get_result($stmt);
            $updates = mysqli_fetch_all($updates_result, MYSQLI_ASSOC);
            
            if (!empty($updates)): 
              foreach ($updates as $update): 
            ?>
              <div class="notification-item <?= $update['is_read'] ? '' : 'unread' ?>" 
                   data-id="<?= $update['update_id'] ?>">
                <strong><?= htmlspecialchars($update['project_name']) ?></strong>
                <p><?= htmlspecialchars($update['title']) ?></p>
                <small><?= date('M j, Y g:i a', strtotime($update['update_date'])) ?></small>
              </div>
            <?php 
              endforeach; 
            else: 
            ?>
              <div class="notification-item">
                <p>No updates available</p>
              </div>
            <?php endif; ?>
            <div class="notification-item" style="text-align: center;">
              <a href="project_updates.php">View All Updates</a>
            </div>
          </div>
        </div>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <section class="hero">
    <h1 class="index">Welcome to Yosech Construction</h1>
    <p class="index">Yosech Construction is a trusted construction firm based in Dipolog City, delivering high-quality road infrastructure, residential and commercial buildings, flood control <br>projects, and reliable equipment rentals. With decades of experience, we blend traditional craftsmanship with modern techniques to create structures that stand the test <br>of time while embracing sustainability and cultural authenticity.</p>
    <a href="rentals.php" class="rentals-button">Browse Rentals</a>
  </section>

  <!-- Project Status Section -->
  <section class="projects-overview">
    <h2>Your Projects Status</h2>
    <?php if (empty($projects)): ?>
      <div class="no-projects">
        <p>You don't have any active projects yet.</p>
        <a href="Apply.php" class="view-updates">Start a New Project</a>
      </div>
    <?php else: ?>
      <div class="project-cards">
        <?php foreach ($projects as $project): 
          $latest_update = $project_updates[$project['project_id']] ?? null;
          ?>
          <div class="project-card">
            <h3><?= htmlspecialchars($project['project_name']) ?></h3>
            
            <div class="project-status">
              <span>Status:</span>
              <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $project['status'])) ?>">
                <?= htmlspecialchars($project['status']) ?>
              </span>
            </div>
            
            <?php if ($latest_update && $latest_update['progress_percentage'] !== null): ?>
              <div class="progress-container">
                <div class="progress-text">
                  <span>Progress:</span>
                  <span><?= $latest_update['progress_percentage'] ?>%</span>
                </div>
                <div class="progress-bar">
                  <div class="progress-fill" style="width: <?= $latest_update['progress_percentage'] ?>%"></div>
                </div>
              </div>
            <?php endif; ?>
            
            <?php if ($latest_update): ?>
              <div class="last-update">
                Last update: <?= date('M j, Y', strtotime($latest_update['update_date'])) ?>
              </div>
            <?php endif; ?>
            
            <a href="project_updates.php?project_id=<?= $project['project_id'] ?>" class="view-updates">
              View Project Updates
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
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
          <img src="Shape.png" alt="General Contract">
          <div>
            <h3>Road Construction & Infrastructure Projects</h3>
            <p>We build durable roads and vital infrastructure that connect communities.</p>
          </div>
        </div>
        <div class="service">
          <img src="Shape2.png" alt="Project Planning">
          <div>
            <h3>Residential & Commercial Building Construction</h3>
            <p>From homes to businesses, we create safe and modern spaces for living and working.</p>
          </div>
        </div>
        <div class="service">
          <img src="Shape3.png" alt="Refurbishment">
          <div>
            <h3>Flood Control & Drainage Solutions</h3>
            <p>Our projects protect communities with reliable flood control and drainage systems.</p>
          </div>
        </div>
        <div class="service">
          <img src="Shape2.png" alt="Refurbishment">
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
      <p class="white-paragraph">Our project managers and superintendents are very experienced in completing highly specialized, multi-faceted projects. Their ability to foresee and therefore prevent problems is invaluable. Construction mastery aids in the design process of the CM/GC and Design-Build delivery methods because we know what is feasible and have a large repertoire of means and methods. Yoztech is uniquely qualified in tilt-up concrete construction. We know what is feasible and we have the means and methods to accomplish the goal.</p>
    </div>
    <div>
      <h3 class="highlighted-title" style="text-align: center;">Engineering</h3>
      <p class="white-paragraph">Our engineers and designers are qualified to solve the challenges presented in today's design and construction market. We have experience in a variety of projects and delivery methods. We know what is feasible and we have the means and methods to accomplish the goal.</p>
    </div>
    <div>
      <h3 class="highlighted-title" style="text-align: center;">Innovation</h3>
      <p class="white-paragraph">We are committed to continually learning and improving. This pursuit of knowledge drives our team to innovative solutions. We are innovative and will draw on all of our resources to find solutions to meet owner needs, even if that means inventing something new.</p>
    </div>
  </section>

  <script>
    // Notification dropdown toggle
    document.getElementById('notification-toggle').addEventListener('click', function(e) {
      e.preventDefault();
      document.getElementById('notification-dropdown').classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.notification-badge')) {
        document.getElementById('notification-dropdown').classList.remove('show');
      }
    });
    
    // Mark notifications as read when clicked
    document.querySelectorAll('.notification-item').forEach(item => {
      item.addEventListener('click', function() {
        if (this.classList.contains('unread')) {
          const updateId = this.dataset.id;
          // Send AJAX request to mark as read
          fetch('mark_read.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'update_id=' + updateId
          });
          
          this.classList.remove('unread');
          // Update badge count
          const badge = document.querySelector('.notification-count');
          if (badge) {
            const currentCount = parseInt(badge.textContent);
            if (currentCount > 1) {
              badge.textContent = currentCount - 1;
            } else {
              badge.remove();
            }
          }
        }
      });
    });
    
    // Mark all as read
    document.querySelector('.mark-all-read').addEventListener('click', function(e) {
      e.stopPropagation();
      fetch('mark_all_read.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'client_id=<?= $client_id ?>'
      }).then(() => {
        document.querySelectorAll('.notification-item.unread').forEach(item => {
          item.classList.remove('unread');
        });
        document.querySelector('.notification-count')?.remove();
      });
    });

    // Animate progress bars on scroll into view
    const progressBars = document.querySelectorAll('.progress-fill');
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const width = entry.target.style.width;
          entry.target.style.width = '0%';
          setTimeout(() => {
            entry.target.style.width = width;
          }, 100);
        }
      });
    }, { threshold: 0.5 });

    progressBars.forEach(bar => {
      observer.observe(bar);
    });
  </script>
  
  <?php include 'footer.php'; ?>
</body>
</html>