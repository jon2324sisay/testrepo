<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Welcome</title>
    <!-- Local Bootstrap CSS (Offline Compatible) -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #1a365d 0%, #2a4d7c 100%);
            color: white;
            padding: 8% 0;
        }
        .feature-card {
            transition: transform 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .footer-custom {
            background-color: #1a365d;
            color: white;
        }
        html {
            scroll-behavior: smooth; /* Enables smooth scrolling when clicking menu links */
        }
        /* Style for Navbar links spacing */
        .nav-item-custom {
            padding-left: 20px;
            padding-right: 20px;
        }
        /* Custom styles for Mission & Vision top borders */
        .card-mission {
            border-top: 4px solid #1a365d !important;
        }
        .card-vision {
            border-top: 4px solid #198754 !important;
        }
    </style>
</head>
<body>

<!-- Navigation Bar (Upgraded with Centered Menu & Right-Aligned Button) -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3 sticky-top">
    <div class="container">
        <!-- Far Left: Brand Logo -->
        <a class="navbar-brand fw-bold text-primary" href="index.php" style="color: #1a365d !important;">
            <i class="bi bi-folder-fill me-2"></i>Office File Management
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Center: Menu Links with custom horizontal spacing -->
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item nav-item-custom"><a class="nav-link active fw-semibold" href="index.php">Home</a></li>
                <li class="nav-item nav-item-custom"><a class="nav-link fw-semibold" href="#services">Services</a></li>
                <li class="nav-item nav-item-custom"><a class="nav-link fw-semibold" href="#about">About Us</a></li>
                <li class="nav-item nav-item-custom"><a class="nav-link fw-semibold" href="#contact">Contact Us</a></li>
            </ul>
            <!-- Far Right: Login Button -->
            <a href="login.php" class="btn btn-primary fw-bold px-4" style="background-color: #1a365d; border: none;">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login to Portal
            </a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1 class="display-4 fw-bold mb-3">Modern & Secure Office File Management</h1>
                <p class="lead mb-4">Digitize your administrative archives, send and receive documents instantly from your desk, and secure organizational memory forever.</p>
                <a href="login.php" class="btn btn-light btn-lg fw-bold text-primary px-5 shadow" style="color: #1a365d !important;">
                    Get Started <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="col-md-5 text-center d-none d-md-block">
                <!-- Beautiful Inline SVG Illustration -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="100%" height="280">
                    <ellipse cx="250" cy="420" rx="180" ry="15" fill="#1a365d" opacity="0.3"/>
                    <rect x="100" y="160" width="300" height="240" rx="15" fill="#2a4d7c" opacity="0.9" />
                    <rect x="120" y="140" width="260" height="240" rx="10" fill="#3b629b" />
                    <rect x="140" y="170" width="220" height="50" rx="5" fill="#eef2f7" />
                    <circle cx="250" cy="195" r="10" fill="#ffc107" />
                    <rect x="140" y="240" width="220" height="50" rx="5" fill="#eef2f7" />
                    <circle cx="250" cy="265" r="10" fill="#ffc107" />
                    <rect x="140" y="310" width="220" height="50" rx="5" fill="#eef2f7" />
                    <circle cx="250" cy="335" r="10" fill="#ffc107" />
                    <g transform="translate(180, 50)">
                        <rect x="0" y="0" width="140" height="180" rx="10" fill="#ffffff" stroke="#198754" stroke-width="5" />
                        <path d="M25 40 H115 M25 70 H115 M25 100 H85" stroke="#a0aec0" stroke-width="4" stroke-linecap="round" />
                        <circle cx="100" cy="120" r="22" fill="#198754" />
                        <path d="M90 120 L97 130 L112 112" stroke="#ffffff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                    </g>
                </svg>
            </div>
        </div>
    </div>
</header>

<!-- System Services Section -->
<section id="services" class="container my-5 py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">System Key Services</h2>
        <p class="text-secondary">Explore the powerful digital capabilities of our archive system.</p>
    </div>
    <div class="row">
        <!-- Service 1 -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm p-4 feature-card">
                <div class="text-primary mb-3">
                    <i class="bi bi-cloud-arrow-up-fill" style="font-size: 2.5rem; color: #1a365d;"></i>
                </div>
                <h5 class="fw-bold">Instant Upload & Routing</h5>
                <p class="text-secondary mb-0">Register document metadata and upload scanned files instantly to specific departments without leaving your desk.</p>
            </div>
        </div>
        <!-- Service 2 -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm p-4 feature-card">
                <div class="text-primary mb-3">
                    <i class="bi bi-search" style="font-size: 2.5rem; color: #1a365d;"></i>
                </div>
                <h5 class="fw-bold">Search Documents</h5>
                <p class="text-secondary mb-0">Retrieve any letter or document from the archive within seconds using keyword search or department filters.</p>
            </div>
        </div>
        <!-- Service 3 -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm p-4 feature-card">
                <div class="text-primary mb-3">
                    <i class="bi bi-shield-lock-fill" style="font-size: 2.5rem; color: #1a365d;"></i>
                </div>
                <h5 class="fw-bold">Role-Based Access</h5>
                <p class="text-secondary mb-0">Protect confidential business correspondence through strict permissions and user role boundaries.</p>
            </div>
        </div>
    </div>
</section>

<!-- About Us, Mission & Vision Section (Upgraded: Icon is closer, and Mission/Vision cards are beautifully added!) -->
<section id="about" class="bg-white py-5 border-top">
    <div class="container my-4">
        <!-- Top Row: About Us Text and Icon (Icon closer) -->
        <div class="row align-items-center mb-5">
            <div class="col-md-4 mb-4 mb-md-0 text-center">
                <i class="bi bi-building-fill text-secondary opacity-25" style="font-size: 8rem;"></i>
            </div>
            <div class="col-md-8">
                <h2 class="fw-bold text-dark mb-3">About Us</h2>
                <p class="text-secondary" style="line-height: 1.8;">This portal is developed to eliminate administrative bottlenecks caused by paper filing. By transforming physical files into secure digital assets, the organization ensures data transparency, fast document routing, and physical storage space optimization.</p>
            </div>
        </div>

        <!-- Bottom Row: Mission and Vision Cards -->
        <div class="row justify-content-center g-4 mt-2">
            <!-- Mission Card -->
            <div class="col-md-5">
                <div class="card h-100 border-0 shadow-sm p-4 card-mission" style="background-color: #f8f9fa;">
                    <div class="mb-3">
                        <i class="bi bi-target" style="font-size: 2.5rem; color: #1a365d;"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Our Mission</h5>
                    <p class="text-secondary mb-0" style="font-size: 1.05rem; line-height: 1.7;">To provide office administrations with a secure, fast, and digital platform to upload, search, and transfer files instantly, eliminating manual paper bottlenecks and improving daily productivity.</p>
                </div>
            </div>
            <!-- Vision Card -->
            <div class="col-md-5">
                <div class="card h-100 border-0 shadow-sm p-4 card-vision" style="background-color: #f8f9fa;">
                    <div class="mb-3">
                        <i class="bi bi-eye-fill" style="font-size: 2.5rem; color: #198754;"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Our Vision</h5>
                    <p class="text-secondary mb-0" style="font-size: 1.05rem; line-height: 1.7;">To transform higher education institutions and government offices into completely paperless, highly secure, and automated digital administrative environments across Ethiopia.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Us Section (CORRECTED: Columns closer and beautifully centered) -->
<section id="contact" class="container my-5 py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Contact Us</h2>
        <p class="text-secondary">Get in touch with Debre Tabor University IT Administration.</p>
    </div>
    <div class="row justify-content-center">
        <!-- Wrapper to pull columns inward (col-md-10) -->
        <div class="col-md-10">
            <div class="row justify-content-center g-4">
                <!-- Location -->
                <div class="col-md-4 text-center">
                    <div class="p-3">
                        <i class="bi bi-geo-alt-fill text-primary fs-3 mb-2" style="color: #1a365d !important;"></i>
                        <h5 class="fw-bold">Location</h5>
                        <p class="text-secondary">Debre Tabor, Ethiopia</p>
                    </div>
                </div>
                <!-- Email -->
                <div class="col-md-4 text-center">
                    <div class="p-3">
                        <i class="bi bi-envelope-fill text-primary fs-3 mb-2" style="color: #1a365d !important;"></i>
                        <h5 class="fw-bold">Email</h5>
                        <p class="text-secondary">info@dtu.edu.et</p>
                    </div>
                </div>
                <!-- Phone -->
                <div class="col-md-4 text-center">
                    <div class="p-3">
                        <i class="bi bi-telephone-fill text-primary fs-3 mb-2" style="color: #1a365d !important;"></i>
                        <h5 class="fw-bold">Phone</h5>
                        <p class="text-secondary">+251-58-123-4567</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-custom py-4 mt-auto">
    <div class="container text-center">
        <p class="mb-1 fw-semibold">Office File Management System &copy; 2026</p>
        <small class="text-light opacity-50">Debre Tabor University - GIT - Department of Computer Science</small>
    </div>
</footer>

<!-- Local Bootstrap Bundle with Popper (Offline Compatible!) -->
<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>