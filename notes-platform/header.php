<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Digital Notes Platform'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon">
    <style>
        /* Add this to your existing CSS */
        .main-nav ul {
            display: flex;
            gap: 15px; /* Space between menu items */
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .main-nav ul li a {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .main-nav ul li a.active {
            background: #3498db;
            color: white;
            border-radius: 4px;
        }
        
        .main-nav ul li a:hover {
            background: cyan;
            color: white;
            border-radius: 4px;
        }
        
        /* Mobile menu styles */
        @media (max-width: 768px) {
            .main-nav ul {
                flex-direction: column;
                gap: 10px;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: white;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
                display: none;
            }
            
            .main-nav ul.active {
                display: flex;
            }
            
            .main-nav ul li a {
                padding: 12px 20px;
                color: #2c3e50;
            }
            
            .main-nav ul li a:hover {
                color: white;
            }
        }
    </style>
</head>
<body>
<header class="site-header">
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <img src="icons/logo.png" alt="Digital Notes Logo">
                <span>Digital Notes Platform</span>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <?php
                $currentPage = basename($_SERVER['PHP_SELF']);
                $navItems = [
                    'index.php' => ['icon' => 'fa-home', 'text' => 'Home'],
                    'about.php' => ['icon' => 'fa-info-circle', 'text' => 'About'],
                    'contact.php' => ['icon' => 'fa-envelope', 'text' => 'Contact'],
                    'donate.php' => ['icon' => 'fa-heart', 'text' => 'Donate'],
                    'notes.php' => ['icon' => 'fa-sticky-note', 'text' => 'Notes'],
                    'past_papers.php' => ['icon' => 'fa-file-pdf', 'text' => 'Past Papers'],
                    'job.php' => ['icon' => 'fa-briefcase', 'text' => 'Jobs'],
                    'scholarship.php' => ['icon' => 'fa-graduation-cap', 'text' => 'Scholarships']
                ];
                
                foreach ($navItems as $page => $item): 
                    $isActive = ($currentPage === $page) ? 'active' : '';
                ?>
                    <li>
                        <a href="<?= $page ?>" class="<?= $isActive ?>">
                            <i class="fas <?= $item['icon'] ?>"></i> 
                            <span><?= $item['text'] ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>
<main class="main-content">

<script>
    // Mobile menu toggle functionality
    document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
        document.querySelector('.main-nav ul').classList.toggle('active');
    });
    
    // Close mobile menu when clicking on a link
    document.querySelectorAll('.main-nav ul li a').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelector('.main-nav ul').classList.remove('active');
        });
    });
</script>