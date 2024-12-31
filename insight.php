<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insight</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4caf50; /* Green */
            --secondary-color: #ffffff; /* White */
            --accent-color: #388e3c; /* Darker Green */
            --text-color: #333;
            --background-color: #f9f9f9;
            --button-hover-color: #2c6f2d;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --max-width: 1200px;
        }

        /* Dark Mode Variables */
        [data-theme="dark"] {
            --primary-color: #81c784; /* Light Green */
            --secondary-color: #333333;
            --accent-color: #66bb6a;
            --text-color: #f9f9f9;
            --background-color: #212121;
            --button-hover-color: #2c6f2d;
            --shadow-color: rgba(0, 0, 0, 0.2);
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            scroll-behavior: smooth;
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: right;
            padding: 15px 50px;
            position: fixed;
            top: 0;
            width: 100%;
            background: var(--secondary-color);
            box-shadow: 0 2px 10px var(--shadow-color);
            z-index: 1000;
            transition: background-color 0.3s;
        }

        .navbar .logo {
            display: flex;
            align-items: center;
            font-size: 26px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .navbar .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .navbar .nav-links {
            display: flex;
            gap: 25px;
        }

        .navbar .nav-links a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            transition: color 0.3s, background-color 0.3s;
        }
    

        .navbar .nav-links a:hover {
            color: var(--secondary-color);
            background-color: var(--primary-color);
        }

        .navbar .hamburger {
            display: none;
            cursor: pointer;
        }

        .navbar .hamburger div {
            width: 30px;
            height: 4px;
            background-color: var(--text-color);
            margin: 6px 0;
            transition: 0.3s;
        }

        .navbar.active .hamburger div:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .navbar.active .hamburger div:nth-child(2) {
            opacity: 0;
        }

        .navbar.active .hamburger div:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        .navbar .nav-links.active {
            display: flex;
            flex-direction: column;
            gap: 15px;
            position: absolute;
            top: 70px;
            right: 30px;
            background: var(--secondary-color);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px var(--shadow-color);
        }
        .service-card a {
    text-decoration: none;
    color: inherit;
    display: block;
    height: 100%;
}
        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: url('logo2.png') no-repeat center center/cover;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            transition: background 0.5s ease-in-out;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Dark overlay */
            z-index: 1;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            z-index: 2;
            animation: fadeIn 1s ease-out;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            max-width: 700px;
            z-index: 2;
            animation: fadeIn 1s ease-out 0.5s;
        }

        .hero button {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            z-index: 2;
            transition: background-color 0.3s;
            animation: fadeIn 1s ease-out 1s;
        }

        .hero button:hover {
            background-color: var(--button-hover-color);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Section Styles */
        section {
            padding: 60px 20px;
            max-width: var(--max-width);
            margin: 0 auto;
        }

        section h2 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 40px;
            color: var(--primary-color);
        }

        /* Services Section */
        .services {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .service-card {
            flex: 1;
            max-width: 300px;
            text-align: center;
            background: var(--secondary-color);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 4px 20px var(--shadow-color);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.2);
        }

        .service-card i {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .service-card h3 {
            margin-bottom: 15px;
            font-size: 22px;
            font-weight: 600;
        }

        /* Gallery Section */
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .gallery img {
            width: 100%;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 20px var(--shadow-color);
        }

        /* Testimonials Section */
        .testimonials-container {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            margin-top: 40px;
        }

        .testimonial-card {
            flex: 0 0 300px;
            background: var(--secondary-color);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 4px 20px var(--shadow-color);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.2);
        }

        .testimonial-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        /* Footer */
        .footer {
            background: var(--primary-color);
            padding: 20px;
            text-align: center;
            color: var(--secondary-color);
            margin-top: 60px;
        }

        .theme-toggle {
            cursor: pointer;
            background-color: var(--primary-color);
            color: var(--secondary-color);
            padding: 10px 20px;
            border-radius: 50px;
            transition: background-color 0.3s;
        }

        .theme-toggle:hover {
            background-color: var(--accent-color);
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px;
            }

            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 18px;
            }

            .services {
                flex-direction: column;
                align-items: center;
            }

            .gallery {
                grid-template-columns: 1fr 1fr;
            }

            .testimonials-container {
                flex-direction: column;
            }

            .navbar .hamburger {
                display: block;
            }

            .navbar .nav-links {
                display: none;
            }

            .navbar.active .nav-links {
                display: flex;
                flex-direction: column;
                gap: 15px;
                background: var(--secondary-color);
                position: absolute;
                top: 70px;
                right: 30px;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 2px 10px var(--shadow-color);
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <img src="logo2.png" alt="Logo">
            Insight
        </div>
        <div class="nav-links">
        
            <a href="#services">Services</a>
           
        </div>
        <div class="hamburger" onclick="toggleNav()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="theme-toggle" onclick="toggleTheme()">🌙</div>
    </div>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <h1>Welcome to Insight</h1>
        <p>Explore innovative ideas and premium services to meet your needs.</p>
        <button>Learn More</button>
    </section>

    <!-- Services Section -->
   <!-- Services Section -->
<section id="services">
    <h2>Our Services</h2>
    <div class="services">
        <div class="service-card">
            <a href="housemaster.php">
                <i class="fas fa-user-tie"></i>
                <h3>Housemaster</h3>
                <p>Providing leadership and support for all house activities.</p>
            </a>
        </div>
        <div class="service-card">
            <a href="dining.php">
                <i class="fas fa-utensils"></i>
                <h3>Dining Hall</h3>
                <p>Serving healthy and nutritious meals for students.</p>
            </a>
        </div>
        <div class="service-card">
            <a href="infirmary.php">
                <i class="fas fa-hospital"></i>
                <h3>Infirmary</h3>
                <p>Offering healthcare and medical services for students.</p>
            </a>
        </div>
        <div class="service-card">
            <a href="exeat.php">
                <i class="fas fa-book"></i>
                <h3>Exeat</h3>
                <p>Managing the exeat process for students' holidays.</p>
            </a>
        </div>
    </div>
</section>

  

  

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Insight. All rights reserved.</p>
    </div>

    <script>
        // Dark Mode / Light Mode Toggle
        function toggleTheme() {
            let currentTheme = document.documentElement.getAttribute('data-theme');
            if (currentTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'light');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        }

        // Navbar Toggle for Mobile
        function toggleNav() {
            document.querySelector('.navbar').classList.toggle('active');
        }
    </script>
</body>
</html>
