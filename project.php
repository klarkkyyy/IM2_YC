<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Construction Solutions - Apply Now</title>
  <link rel="stylesheet" href="style.css" />
  <style>


    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      background-color: #f4f4f4;
    }


    .header {
      background: url('Background.jpg') no-repeat center center/cover;
      position: relative;
      height: 40vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
    }

    .header {
      background: url('Background.jpg') no-repeat center center/cover;
      position: relative;
      height: 40vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
    }

    .header::before {
      content: "";
      position: absolute;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 0;
    }

    .header h1 {
      font-size: 3rem;
      margin: 0;
      position: relative;
      z-index: 1;
    }

    .header p {
      font-size: 1.25rem;
      margin: 1rem 0;
      position: relative;
      z-index: 1;
    }

    .application-form {
      max-width: 700px;
      margin: 2rem auto;
      padding: 2rem;
      background-color: white;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    .application-form h2 {
      font-size: 1.5rem;
      color: #333;
      margin-bottom: 1.5rem;
    }

    .application-form label {
      display: block;
      margin-bottom: 0.5rem;
      color: #333;
    }

    .application-form input {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .application-form .purpose-buttons {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-top: 1.5rem;
    }

    .application-form .purpose-buttons a {
      text-decoration: none;
    }

    .application-form .purpose-buttons button {
      background-color: #004AAD;
      color: white;
      border: none;
      padding: 0.8rem 1.5rem;
      border-radius: 30px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .application-form .purpose-buttons button:hover {
      background-color: #003080;
    }
    footer {
      background-color: #004AAD;
      color: white;
      display: flex;
      justify-content: space-between;
      padding: 20px;
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

    .forlogo {
      display: flex;
      height: 120px;
      width: 100px;
    }

    .social-icons a img {
      height: 20px;
      margin-right: 5px;
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
      }
      .application-form .purpose-buttons {
        flex-direction: column;
        gap: 1rem;
      }
    }





    .navbar-center {
        display: flex;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
        justify-content: flex-start;
    }


    .rent-button-wrapper {
      display: flex;
      justify-content: center;
      margin-top: 10px;
    }

    .navbar-right {
      margin-left: auto; /* Pushes it to the right */
      display: flex;
      align-items: center;
      margin-right: 30px;
    }


    @media (max-width: 768px) {
        .navbar {
          flex-direction: column;
        }
        .application-form .purpose-buttons {
          flex-direction: column;
          gap: 1rem;
        }
      }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>
  <main>
  <section class="header">
    <h1 class="apply">Start Your Project or Equipment Rental Today</h1>
    <p class="apply">Ready to bring your plans to life? Submit your application below to start a new construction project or to rent the equipment you need.</p>
  </section>

  <div class="application-form">
    <h2>Application Form</h2>

  <div class="form-details">
  
  </div>
    <form>
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="phone">Phone:</label>
      <input type="tel" id="phone" name="phone" required>

      <label for="company">Company Name:</label>
      <input type="text" id="company" name="company">

      <div class="purpose-buttons">
        <a href="apply_project.php"><button type="button">Apply for a Project</button></a>
      </div>
    </form>
  </div>
    </main>
  
  <?php include 'footer.php'; ?>
</body>
</html>
