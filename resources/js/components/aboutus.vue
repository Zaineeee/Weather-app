<template>
    <div class="header">
        <div class="logo">Weather Forecast</div>
        <div class="nav-links">
            <a href="#">About Us</a>
            <a href="#">Blog</a>
            <a href="#">Monitoring</a>
            <a href="#">Forecast</a>
            <a href="#" class="sign-in">Sign In</a>
        </div>
    </div>

    <div class="content-section">
        <h1>About Us</h1>
        <p>At Weather App, our mission is to empower you with accurate and timely weather information, no matter where you are. We understand that weather plays a crucial role in our daily lives, from planning outdoor activities to preparing for severe weather conditions. That's why we've created an intuitive and user-friendly platform that tracks weather statistics in real-time.</p>
        <p>Our dedicated team of meteorology enthusiasts and tech experts is committed to providing you with the most reliable forecasts and insights. We take pride in setting high standards for weather reporting and continuously improving our services to meet your needs.</p>
        <p>We believe that staying informed about weather changes is crucial. With features designed for everyone—from casual users to weather enthusiasts—Weather App makes it simple to stay ahead of the elements.</p>
    </div>

    <div class="content-section">
        <h2>Blog</h2>
        <h3>What We Do</h3>
        <p>Weather affects our lives in countless ways, and being prepared is essential. Our app provides real-time weather tracking, detailed forecasts, and severe weather alerts to keep you informed and safe. With our comprehensive coverage of global weather patterns and a full suite of tools about tomorrow's forecast, we've got you covered.</p>
    </div>

    <div class="content-section vision-section">
        <h2>Our Vision</h2>
        <p>We believe that everyone deserves access to reliable weather information. Our goal is to make weather data not only available but also easy to understand. With our user-friendly interface and detailed insights, we're working to keep communities informed and proactive.</p>
    </div>
</template>

<style>
        body {
            background: linear-gradient(to bottom, #7E9DFE, #02050C);
            margin: 0;
            font-family: Arial, 'Times New Roman', Times, serif;
            color: rgb(251, 250, 250);
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 20px;
            background: linear-gradient(to right, #111827, rgba(31, 41, 55, 0.48), #111827);
            border-radius: 8px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
        }

        .content-section {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 30px;
        margin: 20px auto; /* Changed from '20px 0' to '20px auto' */
        backdrop-filter: blur(10px);
        max-width: 800px; /* Added max-width */
    }

        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .vision-section {
            margin-top: 40px;
        }
</style>