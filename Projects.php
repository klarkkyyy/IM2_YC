<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Projects - Construction Solution</title>
  <style>
  body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  position: relative;
  z-index: 0;
}

  body::before {
  content: "";
  opacity: 0.3;
  background-image: url('construction site with cranes in_15536341.png'); 
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}

 .navbar {
    background-color: #004AAD;
    padding: 1.25rem 0;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    padding-left: 40px;

}

.navbar-center {
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}


.logo-centered {
height: 30px; 
}

.nav-links {
display: flex;
gap: 2.0rem;
flex-wrap: wrap;
}

.nav-links a {
color: white;
text-decoration: none;
font-weight: bold;
font-size: 1rem;
}

.nav-links a:hover {
text-decoration: none;
}

.navbar-right {
  margin-left: auto;
  display: flex;
  align-items: center;
  margin-right: 30px;
}
    footer {
      background-color: #004AAD;
      color: white;
      display: flex;
      justify-content: space-between;
      padding: 20px;
      width: 100%;
      margin-top: 40px;
      flex-wrap: wrap;
    }

    .footer-section {
      flex: 1;
      padding: 10px;
    }

    .footer-section h3 {
      margin-top: 0;
      font-size: 1.2em;
    }

    .footer-section p {
      margin: 5px 0;
    }

    .footer-logo {
      width: 150px; 
      height: auto; 
    }

    .social-icons {
      display: flex;
      justify-content: center;
      margin-top: 10px;
    }

    .social-icons a {
      color: white;
      margin: 0 10px;
      text-decoration: none;
      display: flex;
      align-items: center;
    }

    .social-icons a img {
      height: 20px;
      margin-right: 5px;
    }

    .projects-main {
      background-color: transparent;
      padding: 60px 20px;
    }

    .projects-container {
      max-width: 1100px;
      margin: 0 auto;
      text-align: center;
    }

    .projects-container h1 {
      font-size: 3rem;
      color: #004AAD;
      margin-bottom: 10px;
    }

    .projects-container h1 span {
      display: block;
      font-weight: normal;
      font-size: 1.5rem;
      color: #333;
    }

    .projects-container .subtitle {
      max-width: 800px;
      margin: 0 auto 50px;
      font-size: 1rem;
      color: #000000;
    }

    .project-block {
      background: #ffffffcc;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      border-radius: 15px;
      overflow: hidden;
      margin-bottom: 40px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }

    .project-block.reverse {
      flex-direction: row-reverse;
    }

    .project-text {
      flex: 1 1 300px;
      padding: 30px;
      text-align: left;
    }

    .project-text h2 {
      color: #004AAD;
      margin-bottom: 10px;
    }

    .project-text p {
      color: #333;
      font-size: 1rem;
    }

    .project-image {
      flex: 1 1 300px;
      min-width: 300px;
      max-height: 250px;
      overflow: hidden;
    }

    .project-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .pagination {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 40px;
      flex-wrap: wrap;
    }

    .pagination button {
      background-color: #004AAD;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
      font-size: 0.9rem;
    }

    .pagination button.active {
      background-color: #00307d;
    }

    .pagination button:disabled {
      background-color: #888;
      cursor: not-allowed;
    }

    @media (max-width: 768px) {
      .projects-container h1 {
        font-size: 2.2rem;
      }
      .project-block {
        flex-direction: column;
      }
      .project-image {
        min-width: 100%;
      }
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <main>
  <div class="projects-main">
    <div class="projects-container">
      <h1>Construction solution <span>for everyone</span></h1>
      <p class="subtitle">
        We take pride in every project we’ve completed — big or small, simple or complex. Our team brings consistency, quality, and commitment to each build, ensuring every structure or improvement stands the test of time.
      </p>

      <!-- PAGE 1 -->
      <div class="projects-page" id="page-1">
        <div class="project-block">
          <div class="project-text">
            <h2>Drainage Canal – Zone 3 Main Street</h2>
            <p><strong>Start Date:</strong> November 20, 2023<br>
               <strong>Completion Date:</strong> January 10, 2024</p>
            <p>This project improved water flow during storms, helping prevent frequent flooding. It included excavation, canal lining, and safety barriers.</p>
          </div>
          <div class="project-image">
            <img src="drainage canal.jfif" alt="Drainage Canal">
          </div>
        </div>

        <div class="project-block reverse">
          <div class="project-text">
            <h2>2-Story Commercial Building</h2>
            <p><strong>Start Date:</strong> August 15, 2023<br>
               <strong>Completion Date:</strong> December 1, 2023</p>
            <p>A modern two-story structure built to accommodate retail and office spaces. This project showcases our capacity for vertical construction, combining structural integrity with clean design.</p>
          </div>
          <div class="project-image">
            <img src="2storycommericial.jfif" alt="2-Story Commercial Building">
          </div>
        </div>

        <div class="project-block">
          <div class="project-text">
            <h2>Underground Conveyor Tunnel</h2>
            <p><strong>Started:</strong> March 20, 2025</p>
            <p>A specialized infrastructure project designed to support the efficient transport of materials across an industrial facility. The tunnel includes reinforced walls and integrated safety systems to ensure long-term durability and smooth operation.</p>
          </div>
          <div class="project-image">
            <img src="belt-conveyor-in-an-underground-tunnel-transportation-of-ore-to-the-surface-2GY7D0Y.jpg" alt="Underground Conveyor Tunnel">
          </div>
        </div>
      </div>

      <!-- PAGE 2 -->
      <div class="projects-page" id="page-2" style="display:none;">
        <div class="project-block">
          <div class="project-text">
            <h2>Warehouse Expansion Project</h2>
            <p><strong>Start Date:</strong> February 10, 2024<br>
               <strong>Completion Date:</strong> June 30, 2024</p>
            <p>This warehouse expansion increased storage capacity and introduced modern logistics systems for optimized inventory management.</p>
          </div>
          <div class="project-image">
            <img src="warehouseexpansion.jfif" alt="Warehouse Expansion">
          </div>
        </div>

        <div class="project-block reverse">
          <div class="project-text">
            <h2>City Park Revitalization</h2>
            <p><strong>Start Date:</strong> May 15, 2024<br>
               <strong>Completion Date:</strong> September 15, 2024</p>
            <p>This project transformed an aging city park into a vibrant community space with new walking paths, landscaping, and modern amenities.</p>
          </div>
          <div class="project-image">
            <img src="citypark.jpg" alt="City Park Revitalization">
          </div>
        </div>
      </div>

      <!-- PAGE 3 -->
      <div class="projects-page" id="page-3" style="display:none;">
        <div class="project-block">
          <div class="project-text">
            <h2>Highway Bridge Replacement</h2>
            <p><strong>Start Date:</strong> July 1, 2024<br>
               <strong>Completion Date:</strong> November 30, 2024</p>
            <p>Replacement of an aging highway bridge with a modern structure, improving traffic flow and safety for commuters and freight transport.</p>
          </div>
          <div class="project-image">
            <img src="replacement_of_overflow_br_aurora_1.jpg" alt="Highway Bridge Replacement">
          </div>
        </div>
      </div>

      <!-- PAGE 4 (New) -->
      <div class="projects-page" id="page-4" style="display:none;">
        <div class="project-block">
          <div class="project-text">
            <h2>Residential Complex Development</h2>
            <p><strong>Start Date:</strong> January 5, 2023<br>
               <strong>Completion Date:</strong> December 20, 2024</p>
            <p>Construction of a multi-unit residential complex featuring modern apartments, green spaces, and recreational facilities. Designed for sustainable living.</p>
          </div>
          <div class="project-image">
            <img src="project1.jpg" alt="Residential Complex Development">
          </div>
        </div>

        <div class="project-block reverse">
          <div class="project-text">
            <h2>Commercial Office Tower</h2>
            <p><strong>Start Date:</strong> April 1, 2024<br>
               <strong>Completion Date:</strong> March 30, 2026</p>
            <p>Development of a high-rise commercial office tower with state-of-the-art facilities, smart building technology, and panoramic city views.</p>
          </div>
          <div class="project-image">
            <img src="project2.jpg" alt="Commercial Office Tower">
          </div>
        </div>
      </div>

      <!-- PAGE 5 (New) -->
      <div class="projects-page" id="page-5" style="display:none;">
        <div class="project-block">
          <div class="project-text">
            <h2>Coastal Erosion Protection</h2>
            <p><strong>Start Date:</strong> June 1, 2024<br>
               <strong>Completion Date:</strong> February 28, 2025</p>
            <p>Implementation of coastal defense structures, including seawalls and breakwaters, to protect vulnerable coastlines from erosion and storm surges.</p>
          </div>
          <div class="project-image">
            <img src="project3.jpg" alt="Coastal Erosion Protection">
          </div>
        </div>

        <div class="project-block reverse">
          <div class="project-text">
            <h2>Renewable Energy Plant Construction</h2>
            <p><strong>Start Date:</strong> September 1, 2023<br>
               <strong>Completion Date:</strong> August 31, 2025</p>
            <p>Building a new solar power plant, including solar panel installation, inverter stations, and grid connection infrastructure, contributing to sustainable energy.</p>
          </div>
          <div class="project-image">
            <img src="project4.jpg" alt="Renewable Energy Plant Construction">
          </div>
        </div>
      </div>

      <div class="pagination">
        <button data-page="1" class="active">1</button>
        <button data-page="2">2</button>
        <button data-page="3">3</button>
        <button data-page="4">4</button>
        <button data-page="5">5</button>
      </div>
    </div>
  </div>
  </main>
  <?php include 'footer.php'; ?>

  <script>
    const pages = document.querySelectorAll('.projects-page');
    const buttons = document.querySelectorAll('.pagination button[data-page]');
    let currentPage = 1;
    const totalPages = pages.length;

    function showPage(pageNum) {
      pages.forEach(p => p.style.display = 'none');
      document.getElementById(`page-${pageNum}`).style.display = 'block';

      buttons.forEach(btn => btn.classList.remove('active'));
      document.querySelector(`.pagination button[data-page="${pageNum}"]`)?.classList.add('active');

      currentPage = pageNum;
    }

    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        const page = btn.dataset.page;
        if (page) {
          showPage(Number(page));
        }
      });
    });

    // Removed the 'nextBtn' event listener as it's not present in the HTML
    // document.getElementById('nextBtn').addEventListener('click', () => {
    //   let nextPage = currentPage + 1;
    //   if (nextPage > totalPages) nextPage = 1;
    //   showPage(nextPage);
    // });

    
    showPage(currentPage);
  </script>
</body>
</html>
