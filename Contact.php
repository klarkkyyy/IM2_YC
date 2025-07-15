<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Construction Solutions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .white-box {
            background-color: #fff;
            border: 2px solid #004AAD;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
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
            height: 35px; 
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
            text-decoration: underline;
        }


        .contact-section {
            padding: 4rem 2rem;
            background-color: #f4f4f4;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .contact-form,
        .contact-info {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 1rem;
            flex: 1;
        }

        .contact-form h2,
        .contact-info h2 {
            margin-bottom: 1rem;
            color: #004AAD;
        }

        .contact-form form div {
            margin-bottom: 1rem;
        }

        .contact-form form label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .contact-form form input,
        .contact-form form textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .contact-form form button {
            background-color: #004AAD;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 1rem;
        }

        .contact-info p {
            margin: 0.5rem 0;
        }

        .map {
            margin: 1rem 0;
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

        .social-icons a img {
            height: 20px;
            margin-right: 5px;
        }

        @media (min-width: 768px) {
            .navbar .left-links a,
            .navbar .right-links a {
                margin: 0 1.5rem;
            }

            .contact-section {
                padding: 6rem 4rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>


    <section class="contact-section">
        <div class="contact-form">
            <h2>Contact Us</h2>
            <form>
                <div>
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit">Send Message</button>
            </form>
        </div>

        <div class="contact-info">
            <h2>Our Office</h2>
            <p><strong>Address:</strong> PUNTA, DIPOLOG CITY, ZAMBOANGA DEL NORTE</p>
            <p><strong>Phone:</strong> 09994801639</p>
            <p><strong>Email:</strong> YosechConstruction@gmail.com</p>
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15783.42198505769!2d123.31564576149158!3d8.513408085810106!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x325491387917f2c7%3A0xe24c9a8f84b6d199!2sPunta%2C%20Dipolog%20City%2C%20Zamboanga%20del%20Norte!5e0!3m2!1sen!2sph!4v1752559775714!5m2!1sen!2sph" width="800" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>