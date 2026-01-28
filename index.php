<?php
require $_SERVER['DOCUMENT_ROOT'] . '/unifreelance/backend/config/db.php';

// Don't auto-redirect - let all users access the landing page
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniFreelance - University Freelancing Platform</title>
    <link rel="stylesheet" href="/unifreelance/frontend/assets/css/style.css">
</head>

<body>
    <header class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <a href="/unifreelance/">UniFreelance</a>
            </div>
            <nav class="navbar-nav">
                <a href="/unifreelance/backend/auth/login.php" class="btn btn-secondary btn-small">Login</a>
                <a href="/unifreelance/backend/auth/register.php" class="btn btn-small">Register</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div style="text-align: center; padding: 3rem 0;">
            <h1 style="color: var(--primary-color); font-size: 3rem;">Welcome to UniFreelance</h1>
            <p style="font-size: 1.3rem; color: var(--light-text); margin: 1rem 0 2rem;">
                Connect university students with meaningful work opportunities
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; max-width: 800px; margin: 0 auto;">
                <div class="card">
                    <h2 style="color: var(--primary-color);">For Students</h2>
                    <p>Find flexible work opportunities that fit your schedule and build your portfolio.</p>
                    <a href="/unifreelance/backend/auth/register.php" class="btn" style="width: 100%;">Get Started</a>
                </div>
                <div class="card">
                    <h2 style="color: var(--secondary-color);">For Clients</h2>
                    <p>Hire talented university students for your projects at competitive rates.</p>
                    <a href="/unifreelance/backend/auth/register.php" class="btn btn-secondary" style="width: 100%;">Post a Job</a>
                </div>
            </div>

            <div style="margin: 4rem 0;">
                <h2>Features</h2>
                <div class="grid-3">
                    <div class="card">
                        <h3>Secure Escrow</h3>
                        <p>Payment held safely until work is completed and verified.</p>
                    </div>
                    <div class="card">
                        <h3>ID Verified</h3>
                        <p>All users are identity verified for trust and security.</p>
                    </div>
                    <div class="card">
                        <h3>Dispute Resolution</h3>
                        <p>Professional mediation for any disagreements.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2026 UniFreelance. All rights reserved.</p>
            <p>University Freelancing Platform</p>
        </div>
    </footer>
</body>

</html>