<?php
$pageTitle = 'Page Not Found - ResourceHub';
require_once __DIR__ . '/models/Category.php';

$categoryModel = new Category();
$categories = $categoryModel->getAll();
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo $pageTitle; ?></title>
    <!-- Tailwind CSS -->
    <script src="public/assets/js/tailwind.js"></script>
    <!-- Google Fonts: Lexend & Material Symbols -->
    <link rel="stylesheet" href="public/assets/css/lexend.css" />
    <link rel="stylesheet" href="public/assets/css/material-symbols.css" />
    <!-- Tailwind Config JS -->
    <script src="public/assets/js/tailwind-config.js"></script>
    <!-- Custom JS -->
    <script src="public/assets/js/custom.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/assets/css/style.css" />
    <link rel="stylesheet" href="public/assets/css/custom.css"/>
</head>
<body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-white min-h-screen">
    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 w-full border-b border-[#f0f2f4] dark:border-gray-800 bg-white dark:bg-[#1a242f] px-4 lg:px-20">
        <div class="max-w-[1440px] mx-auto flex h-16 items-center justify-between gap-8">
            <!-- Logo -->
            <div class="flex items-center gap-3 shrink-0">
                <div class="text-primary">
                    <span class="material-symbols-outlined text-3xl">hub</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight text-[#111418] dark:text-white">ResourceHub</h1>
            </div>
            <!-- Search Bar -->
            <div class="flex-1 max-w-xl">
                <form method="GET" action="index.php" class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                        <span class="material-symbols-outlined text-xl">search</span>
                    </div>
                    <input
                        name="search"
                        class="block w-full rounded-lg border-none bg-background-light dark:bg-gray-800 py-2 pl-10 pr-3 text-sm placeholder-[#617589] focus:ring-2 focus:ring-primary focus:bg-white dark:focus:bg-gray-700 transition-all"
                        placeholder="Search for tools, sites, or topics..."
                        type="text"
                        autocomplete="off" />
                </form>
            </div>
            <!-- Nav Actions -->
            <div class="flex items-center gap-6">
                <nav class="hidden md:flex items-center gap-6">
                    <a class="text-sm font-medium hover:text-primary" href="index.php">Explore</a>
                    <a class="text-sm font-medium hover:text-primary" href="#">Top Leaders</a>
                    <a class="text-sm font-medium hover:text-primary" href="#">Community</a>
                </nav>
                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-xl">dark_mode</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center min-h-[calc(100vh-8rem)] px-4 lg:px-20">
        <div class="max-w-[1440px] mx-auto text-center">
            <!-- 404 Illustration -->
            <div class="mb-8">
                <span class="material-symbols-outlined text-9xl text-gray-300 dark:text-gray-600">error_outline</span>
            </div>

            <!-- Error Message -->
            <h1 class="text-6xl font-black text-gray-900 dark:text-white mb-4">404</h1>
            <h2 class="text-3xl font-bold text-gray-700 dark:text-gray-300 mb-6">Page Not Found</h2>
            <p class="text-xl text-gray-500 dark:text-gray-400 mb-8 max-w-2xl mx-auto">
                Sorry, the page you're looking for doesn't exist. It might have been moved, deleted, or you entered the wrong URL.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="index.php" class="bg-primary text-white px-8 py-3 rounded-lg font-bold hover:bg-primary/90 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined">home</span>
                    Go to Homepage
                </a>
                <button onclick="history.back()" class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-8 py-3 rounded-lg font-bold hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Go Back
                </button>
            </div>

            <!-- Search Suggestion -->
            <div class="mt-12 max-w-md mx-auto">
                <p class="text-gray-500 dark:text-gray-400 mb-4">Or try searching for what you need:</p>
                <form method="GET" action="index.php" class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                        <span class="material-symbols-outlined text-xl">search</span>
                    </div>
                    <input
                        name="search"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-3 pl-10 pr-4 text-sm placeholder-[#617589] focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                        placeholder="Search resources..."
                        type="text"
                        autocomplete="off" />
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#f0f2f4] dark:border-gray-800 bg-white dark:bg-[#1a242f] py-12 px-4 lg:px-20">
        <div class="max-w-[1440px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1 flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="text-primary">
                        <span class="material-symbols-outlined text-3xl">hub</span>
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-[#111418] dark:text-white">ResourceHub</h2>
                </div>
                <p class="text-sm text-[#617589] dark:text-gray-400">Discovering and ranking the web's best tools since 2023.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4 text-[#111418] dark:text-white">Categories</h4>
                <ul class="flex flex-col gap-2 text-sm text-[#617589] dark:text-gray-400">
                    <?php if (!empty($categories)): ?>
                        <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                            <li>
                                <a class="hover:text-primary" href="index.php?category=<?php echo $cat['id']; ?>&category_slug=<?php echo urlencode($cat['slug']); ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a class="hover:text-primary" href="index.php">Development</a></li>
                        <li><a class="hover:text-primary" href="index.php">Graphic Design</a></li>
                        <li><a class="hover:text-primary" href="index.php">Marketing Tools</a></li>
                        <li><a class="hover:text-primary" href="index.php">Education</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4 text-[#111418] dark:text-white">Support</h4>
                <ul class="flex flex-col gap-2 text-sm text-[#617589] dark:text-gray-400">
                    <li><a class="hover:text-primary" href="#">Help Center</a></li>
                    <li><a class="hover:text-primary" href="back-office/index.php">Submit a Resource</a></li>
                    <li><a class="hover:text-primary" href="#">API Documentation</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4 text-[#111418] dark:text-white">Newsletter</h4>
                <p class="text-sm text-[#617589] dark:text-gray-400 mb-4">Get the weekly top leaders list in your inbox.</p>
                <div class="flex gap-2">
                    <input class="flex-1 rounded-lg border-[#f0f2f4] dark:border-gray-800 dark:bg-gray-800 text-sm text-gray-900 dark:text-white" placeholder="Email" type="email" />
                    <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold">Join</button>
                </div>
            </div>
        </div>
        <div class="max-w-[1440px] mx-auto mt-8 pt-8 border-t border-[#f0f2f4] dark:border-gray-800">
            <p class="text-center text-sm text-[#617589] dark:text-gray-400">
                © 2024 ResourceHub. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        html.classList.toggle('dark', currentTheme === 'dark');
        updateThemeIcon();

        themeToggle.addEventListener('click', () => {
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeIcon();
        });

        function updateThemeIcon() {
            const isDark = html.classList.contains('dark');
            themeToggle.innerHTML = `<span class="material-symbols-outlined text-xl">${isDark ? 'light_mode' : 'dark_mode'}</span>`;
        }
    </script>
</body>
</html>
