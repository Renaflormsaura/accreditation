<?php
require 'connection.php';

// Fetch data from tables
function fetchEntries($pdo, $table) {
    $stmt = $pdo->query("SELECT * FROM $table ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

$programs = fetchEntries($pdo, 'program_surveys');
$areas = fetchEntries($pdo, 'area_surveys');
$certificates = fetchEntries($pdo, 'certificate_authenticity');
$abouts = fetchEntries($pdo, 'about_pup_unisan');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Polytechnic University of the Philippines</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <!-- Font Awesome for search icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* General styling and reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background, similar to previous Tailwind */
            color: #333; /* Default text color */
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        
        /* Top Red Header - PUP Unisan Accreditation */
        .top-header {
            background-color: #8c0000; /* Dark red, similar to your screenshot */
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-weight: 700;
            font-size: 2rem; /* Adjusted font size */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 0;
            width: 100%; /* Ensure it spans full width */
            border-radius: 0; /* No rounded corners */
        }

        /* Section for Logo, University Name, Slogan, and Search Bar */
        .university-info-section {
            background-color: white;
            padding: 20px;
            display: flex; /* Use flexbox for alignment */
            align-items: center; /* Vertically center items */
            justify-content: space-between; /* Pushes first item to start, last to end, distributes space in between */
            max-width: 1300px; /* Pinalawak ang container */
            width: 100%; /* Ensure it takes full available width within max-width */
            margin: 0 auto; /* Center the section */
            border-bottom-left-radius: 12px; /* Rounded bottom corners */
            border-bottom-right-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding-bottom: 20px; /* Adjust padding for visual balance */
            flex-wrap: wrap; /* Allow items to wrap on smaller screens if they don't fit */
        }

        /* Styling for the PUP logo */
        .logo {
            width: 100px; /* Size of the logo */
            height: auto; /* Maintain aspect ratio */
            border-radius: 8px;
            flex-shrink: 0; /* Prevent logo from shrinking */
        }

        /* Styling for the university name and slogan text */
        .university-text {
            display: flex;
            flex-direction: column; /* Stack text lines */
            text-align: left; /* Align text to the left */
            color: #333; /* Dark gray text color */
            flex-grow: 1; /* Allow text to grow and take available space */
            margin-left: 20px; /* Add some space from the logo */
            margin-right: 20px; /* Add some space before the search bar */
        }

        .university-text .main-university-name {
            font-size: 1.9rem; /* University name font size */
            font-weight: 600;
            margin-bottom: 5px; /* Space between name and slogan */
            color: #800000; /* Red color for main name */
        }

        .university-text .slogan {
            font-size: 1.1rem; /* Slogan font size */
            font-weight: 400;
            color: #555; /* Lighter gray for slogan */
        }

        /* Search bar within the university-info-section */
        .university-info-section .input-group {
            max-width: 300px; /* Limit width of the search input */
            flex-shrink: 0; /* Prevent search bar from shrinking excessively */
        }
        .university-info-section .input-group input {
            border-radius: 0.25rem; /* Match Bootstrap default */
        }
        .university-info-section .input-group-append .btn {
            border-top-right-radius: 0.25rem; /* Match Bootstrap default */
            border-bottom-right-radius: 0.25rem; /* Match Bootstrap default */
        }


        /* Navigation bar styling */
        .main-navbar {
            background: #a52a2a; /* Reddish background for navbar */
            border-radius: 8px; /* Rounded corners for the entire nav */
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            max-width: 1300px; /* Pinalawak ang container */
            margin: 20px auto 30px auto; /* Center with margin below and above */
        }

        .main-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.2rem;
            color: white !important;
            padding-left: 15px; /* Adjust if needed */
        }

        .main-navbar .navbar-nav .nav-link {
            color: white !important; /* White text for nav links */
            padding: 0.75rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .main-navbar .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2); /* Light hover effect */
        }

        /* Carousel specific overrides */
        .carousel-item img {
            height: 600px;
            object-fit: cover;
            border-radius: 20px;
        }
        .carousel {
            margin-bottom: 3rem;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.5);
            max-width: 1300px; /* Pinalawak ang container */
            margin-left: auto;
            margin-right: auto;
        }

        /* Card styling (from your original code) */
        main {
            max-width: 1300px; /* Pinalawak ang container */
            margin: 2rem auto 4rem;
            padding: 0 1rem;
            color: #333; /* Ensure content in main is visible */
        }
        section {
            margin-bottom: 4rem;
        }
        section h2 {
            border-bottom: 3px solid #800000;
            padding-bottom: 0.25rem;
            margin-bottom: 1rem;
            font-weight: 700;
            font-size: 1.75rem;
            color: #800000;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
            gap: 1.8rem;
        }
        article.card {
            background: #fff; /* White background for cards */
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1); /* Lighter shadow */
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            color: #333; /* Dark text for cards */
        }
        article.card:hover {
            transform: translateY(-8px);
        }
        article.card img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-bottom: 2px solid #a52a2a;
        }
        .card-content {
            padding: 1rem;
            flex-grow: 1; /* Allow content to take available space */
        }
        .card-title {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: #800000;
        }
        .card-description {
            font-size: 0.95rem;
            color: #555;
        }
        footer {
            background: #800000;
            color: #fff;
            text-align: center;
            padding: 1rem 0;
            font-size: 0.9rem;
            margin-top: auto; /* Pushes footer to the bottom */
        }
        /* Responsive adjustments */
        @media (max-width: 1050px) { /* Adjust breakpoint for larger screens */
            .university-info-section {
                flex-wrap: wrap; /* Allow items to wrap */
                justify-content: center; /* Center items when wrapped */
                text-align: center;
                gap: 15px; /* Adjust gap when wrapped */
            }
            .university-text {
                margin-left: 0; /* Remove specific margins when wrapped */
                margin-right: 0;
                text-align: center; /* Center text when wrapped */
                flex-basis: 100%; /* Make text take full width when wrapped */
            }
            .university-info-section .input-group {
                max-width: 400px; /* Give search bar more room when wrapped */
                width: 100%; /* Make it full width if it wraps */
            }
        }

        @media (max-width: 768px) {
            .top-header {
                font-size: 1.8rem;
                padding: 1rem;
            }
            .university-info-section {
                flex-direction: column; /* Stack logo, text, and search bar vertically */
                padding: 15px;
                gap: 10px;
            }
            .university-text .main-university-name {
                font-size: 1.2rem;
            }
            .university-text .slogan {
                font-size: 0.9rem;
            }
            .logo {
                width: 80px;
                margin-bottom: 10px;
            }
            .main-navbar {
                margin: 0 auto 20px auto;
            }
            .carousel-item img {
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            .top-header {
                font-size: 1.5rem;
            }
            .university-info-section {
                padding: 10px;
                gap: 10px;
            }
            .university-text .main-university-name {
                font-size: 1rem;
            }
            .university-text .slogan {
                font-size: 0.8rem;
            }
            .logo {
                width: 60px;
            }
            .main-navbar {
                padding: 0;
            }
            .main-navbar .navbar-nav .nav-link {
                font-size: 0.9rem;
                padding: 0.5rem 0.75rem;
            }
            .carousel-item img {
                height: 200px;
            }
            main {
                padding: 0 0.5rem;
            }
            section h2 {
                font-size: 1.25rem;
            }
            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="flex flex-col items-center">

    <!-- Ang pinaka-itaas na Header bar: PUP Unisan Accreditation -->
    <header class="top-header">
        PUP Unisan Accreditation
    </header>

    <!-- Ang Section na naglalaman ng Logo, University Info, at Search Bar -->
    <div class="university-info-section">
        <img alt="PUP Logo" class="logo" src="pup logo.png">
        <div class="university-text">
            <span class="main-university-name">Polytechnic University of the Philippines</span>
            <span class="slogan hidden-xs">The Country's 1st PolytechnicU</span> </a>
        </div>
        
        <!-- Search bar, nilipat sa loob ng university-info-section -->
        <div class="input-group input-group-sm">
            <input name="ctl00$txtSearch" type="text" id="txtSearch" class="form-control" placeholder="Type keyword here..." />
            <div class="input-group-append">
                <a id="btnSearch" class="btn btn-system btn-flat" href="javascript:WebForm_DoPostBackWithOptions(new WebForm_PostBackOptions(&quot;ctl00$btnSearch&quot;, &quot;&quot;, false, &quot;&quot;, &quot;/search&quot;, false, true))">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark main-navbar">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#featured-programs">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#programs-surveys">Program Under Surveys</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#areas-surveys">Area Under Surveys</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#certificates">Certificate of Authenticity</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about-pup">About PUP Unisan</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main banner image -->
    <img src="certificate.jpg"
         alt="Web Banner - AsiaTech"
         class="huge-it-slide-image">

    <main>
        <section id="featured-programs" aria-label="Featured Program Images Carousel">
            <h2>Featured Program Images</h2>
            <?php if (count($programs) === 0): ?>
                <p>No program survey images available at this time.</p>
            <?php else: ?>
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="4000" data-pause="hover">
                <div class="carousel-inner">
                    <?php foreach ($programs as $index => $program): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="uploads/<?php echo htmlspecialchars($program['image']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($program['title']); ?>" loading="lazy" />
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-target="#carouselExampleControls" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-target="#carouselExampleControls" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </button>
            </div>
            <?php endif; ?>
        </section>

        <section id="programs-surveys" aria-label="Program Under Surveys">
            <h2>Program Under Surveys</h2>
            <?php if (count($programs) === 0): ?>
                <p>No program survey content available at this time.</p>
            <?php else: ?>
            <div class="cards">
                <?php foreach ($programs as $program): ?>
                    <article class="card">
                        <img src="uploads/<?php echo htmlspecialchars($program['image']); ?>" alt="<?php echo htmlspecialchars($program['title']); ?>" loading="lazy" />
                        <div class="card-content">
                            <div class="card-title"><?php echo htmlspecialchars($program['title']); ?></div>
                            <div class="card-description"><?php echo nl2br(htmlspecialchars($program['description'])); ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <section id="areas-surveys" aria-label="Area Under Surveys">
            <h2>Area Under Surveys</h2>
            <?php if (count($areas) === 0): ?>
                <p>No area survey content available at this time.</p>
            <?php else: ?>
            <div class="cards">
                <?php foreach ($areas as $area): ?>
                    <article class="card">
                        <img src="uploads/<?php echo htmlspecialchars($area['image']); ?>" alt="<?php echo htmlspecialchars($area['title']); ?>" loading="lazy" />
                        <div class="card-content">
                            <div class="card-title"><?php echo htmlspecialchars($area['title']); ?></div>
                            <div class="card-description"><?php echo nl2br(htmlspecialchars($area['description'])); ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <section id="certificates" aria-label="Certificate of Authenticity">
            <h2>Certificate of Authenticity</h2>
            <?php if (count($certificates) === 0): ?>
                <p>No certificate content available at this time.</p>
            <?php else: ?>
            <div class="cards">
                <?php foreach ($certificates as $cert): ?>
                    <article class="card">
                        <img src="uploads/<?php echo htmlspecialchars($cert['image']); ?>" alt="<?php echo htmlspecialchars($cert['title']); ?>" loading="lazy" />
                        <div class="card-content">
                            <div class="card-title"><?php echo htmlspecialchars($cert['title']); ?></div>
                            <div class="card-description"><?php echo nl2br(htmlspecialchars($cert['description'])); ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <section id="about-pup" aria-label="About PUP Unisan">
            <h2>About PUP Unisan</h2>
            <?php if (count($abouts) === 0): ?>
                <p>No About PUP Unisan content available at this time.</p>
            <?php else: ?>
            <div class="cards">
                <?php foreach ($abouts as $about): ?>
                    <article class="card">
                        <img src="uploads/<?php echo htmlspecialchars($about['image']); ?>" alt="<?php echo htmlspecialchars($about['title']); ?>" loading="lazy" />
                        <div class="card-content">
                            <div class="card-title"><?php echo htmlspecialchars($about['title']); ?></div>
                            <div class="card-description"><?php echo nl2br(htmlspecialchars($about['description'])); ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> PUP Unisan Accreditation Portal
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
