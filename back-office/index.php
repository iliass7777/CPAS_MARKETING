<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Website.php';
require_once __DIR__ . '/../models/Review.php';

$categoryModel = new Category();
$websiteModel = new Website();
$reviewModel = new Review();

$totalCategories = count($categoryModel->getAll());
$totalWebsites = count($websiteModel->getAll());
$totalReviews = count($reviewModel->getAll());
$pendingReviews = count(array_filter($reviewModel->getAll(), function($r) {
    return $r['status'] === 'pending';
}));
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Admin Back-Office - Dashboard</title>
    <link href="../public/assets/css/lexend.css"
        rel="stylesheet" />
    <link
        href="../public/assets/css/material-symbols.css"
        rel="stylesheet" />
    <script src="../public/assets/js/tailwind.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Lexend"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    <!-- Custom JS -->
    <script src="../public/assets/js/custom.js"></script>
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }
        #sidebar {
            transition: width 0.3s ease;
        }
        #sidebar.collapsed {
            width: 4rem;
        }
        #sidebar.collapsed .sidebar-text {
            display: none;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside id="sidebar"
            class="w-64 bg-white dark:bg-background-dark border-r border-gray-200 dark:border-gray-800 flex flex-col">
            <div class="p-6 flex flex-col gap-8 h-full">
                <!-- Toggle Button -->
                <div class="flex justify-end">
                    <button id="sidebar-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-xl text-gray-700 dark:text-gray-300">menu</span>
                    </button>
                </div>
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <div class="bg-primary size-10 rounded-lg flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">auto_stories</span>
                    </div>
                    <div class="flex flex-col sidebar-text">
                        <h1 class="text-gray-900 dark:text-white text-base font-bold leading-tight">Admin Portal</h1>
                        <p class="text-gray-500 dark:text-gray-400 text-xs font-normal">Back-Office v1.0</p>
                    </div>
                </div>
                <!-- Navigation Links -->
                <nav class="flex flex-col gap-1 grow">
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary" href="index.php">
                        <span class="material-symbols-outlined text-[24px]">dashboard</span>
                        <p class="text-sm font-semibold sidebar-text">Dashboard</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        href="websites.php">
                        <span class="material-symbols-outlined text-[24px]">database</span>
                        <p class="text-sm font-medium sidebar-text">Resources</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        href="categories.php">
                        <span class="material-symbols-outlined text-[24px]">folder</span>
                        <p class="text-sm font-medium sidebar-text">Categories</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        href="reviews.php">
                        <span class="material-symbols-outlined text-[24px]">chat_bubble</span>
                        <p class="text-sm font-medium sidebar-text">User Feedback</p>
                    </a>
                </nav>
                <!-- Profile Footer -->
                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="size-10 rounded-full bg-cover bg-center bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-gray-400">person</span>
                        </div>
                        <div class="flex flex-col overflow-hidden sidebar-text">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin User'); ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo htmlspecialchars($_SESSION['email'] ?? 'admin@platform.com'); ?></p>
                        </div>
                    </div>
                    <a href="logout.php" class="flex items-center gap-2 w-full px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        <span class="text-sm font-medium sidebar-text">Logout</span>
                    </a>
                </div>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-y-auto">
            <!-- Top Navigation Bar -->
            <header
                class="flex items-center justify-between bg-white dark:bg-background-dark border-b border-gray-200 dark:border-gray-800 px-8 py-4 sticky top-0 z-10">
                <div class="flex items-center gap-6 flex-1">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white">Dashboard</h2>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-xl text-gray-700 dark:text-gray-300">dark_mode</span>
                    </button>
                    <a href="../index.php" target="_blank"
                        class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                        <span>View Front Office</span>
                    </a>
                </div>
            </header>
            <!-- Dashboard Body -->
            <div class="p-8 space-y-8">
                <!-- Page Heading -->
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="space-y-1">
                        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Dashboard Overview</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-base">Monitor and manage your platform resources.</p>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Categories</p>
                            <span class="material-symbols-outlined text-purple-500">category</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $totalCategories; ?></p>
                        <div class="mt-2 flex items-center text-xs text-gray-400 font-medium">
                            <span>Active categories</span>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Resources</p>
                            <span class="material-symbols-outlined text-primary">inventory_2</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $totalWebsites; ?></p>
                        <div class="mt-2 flex items-center text-xs text-green-500 font-medium">
                            <span class="material-symbols-outlined text-[14px] mr-1">trending_up</span>
                            <span>Active listings</span>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Reviews</p>
                            <span class="material-symbols-outlined text-blue-500">rate_review</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $totalReviews; ?></p>
                        <div class="mt-2 flex items-center text-xs text-gray-400 font-medium">
                            <span>All reviews</span>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Pending Reviews</p>
                            <span class="material-symbols-outlined text-amber-500">pending_actions</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $pendingReviews; ?></p>
                        <div class="mt-2 flex items-center text-xs text-amber-500 font-medium">
                            <span>Requires attention</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="categories.php?action=create"
                            class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <div class="bg-primary/10 p-3 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-2xl">add</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Create Category</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Add a new category</p>
                            </div>
                        </a>
                        <a href="websites.php?action=create"
                            class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <div class="bg-primary/10 p-3 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-2xl">add</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Create Website</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Add a new resource</p>
                            </div>
                        </a>
                        <a href="reviews.php"
                            class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                            <div class="bg-primary/10 p-3 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-2xl">chat_bubble</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Manage Reviews</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Approve or reject reviews</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
    </script>
</body>

</html>
