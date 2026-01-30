<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../models/Website.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Review.php';

$websiteModel = new Website();
$categoryModel = new Category();
$reviewModel = new Review();
$message = '';
$messageType = '';

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $categoryId = (int)$_POST['category_id'];
        $name = trim($_POST['name']);
        $url = trim($_POST['url']);
        $description = trim($_POST['description']);
        
        if (empty($name) || empty($url) || $categoryId <= 0) {
            $message = 'Name, URL, and Category are required.';
            $messageType = 'error';
        } else {
            $result = $websiteModel->create($categoryId, $name, $url, $description);
            if ($result) {
                $message = 'Website created successfully!';
                $messageType = 'success';
                $action = 'list';
            } else {
                $message = 'Failed to create website.';
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $categoryId = (int)$_POST['category_id'];
        $name = trim($_POST['name']);
        $url = trim($_POST['url']);
        $description = trim($_POST['description']);
        
        if (empty($name) || empty($url) || $categoryId <= 0) {
            $message = 'Name, URL, and Category are required.';
            $messageType = 'error';
        } else {
            $result = $websiteModel->update($id, $categoryId, $name, $url, $description);
            if ($result) {
                $message = 'Website updated successfully!';
                $messageType = 'success';
                $action = 'list';
            } else {
                $message = 'Failed to update website.';
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $result = $websiteModel->delete($id);
        if ($result) {
            $message = 'Website deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to delete website.';
            $messageType = 'error';
        }
        $action = 'list';
    }
}

$websites = $websiteModel->getAll();
$categories = $categoryModel->getAll();
$website = null;

if ($action === 'edit' && $id > 0) {
    $website = $websiteModel->getById($id);
    if (!$website) {
        $action = 'list';
        $message = 'Website not found.';
        $messageType = 'error';
    }
}

// Get stats
 $totalWebsites = count($websites);
 $pendingReviews = count(array_filter($reviewModel->getAll(), function($r) {
    return $r['status'] === 'pending';
}));
$totalCategories = count($categories);
$drawerOpen = in_array($action, ['create', 'edit']);
$drawerTitle = $action === 'edit' ? 'Edit Resource' : 'Add New Resource';
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Admin Back-Office Management</title>
    <!-- Google Fonts & Material Icons -->
    <link href="../public/assets/css/lexend.css"
        rel="stylesheet" />
    <link
        href="../public/assets/css/material-symbols.css"
        rel="stylesheet" />
    <!-- Tailwind CSS -->
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
    <!-- AJAX API Client -->
    <script src="../public/assets/js/api-client.js"></script>
    <!-- Custom JS -->
    <script src="../public/assets/js/custom.js"></script>
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside
            class="w-64 bg-white dark:bg-background-dark border-r border-gray-200 dark:border-gray-800 flex flex-col">
            <div class="p-6 flex flex-col gap-8 h-full">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <div class="bg-primary size-10 rounded-lg flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">auto_stories</span>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-gray-900 dark:text-white text-base font-bold leading-tight">Admin Portal</h1>
                        <p class="text-gray-500 dark:text-gray-400 text-xs font-normal">Back-Office v1.0</p>
                    </div>
                </div>
                <!-- Navigation Links -->
                <nav class="flex flex-col gap-1 grow">
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary" href="websites.php">
                        <span class="material-symbols-outlined text-[24px]">database</span>
                        <p class="text-sm font-semibold">Resources</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        href="categories.php">
                        <span class="material-symbols-outlined text-[24px]">folder</span>
                        <p class="text-sm font-medium">Categories</p>
                    </a>
               
                    <div class="my-4 border-t border-gray-100 dark:border-gray-800"></div>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        href="index.php">
                        <span class="material-symbols-outlined text-[24px]">dashboard</span>
                        <p class="text-sm font-medium">Dashboard</p>
                    </a>
                </nav>
                <!-- Profile Footer -->
                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="size-10 rounded-full bg-cover bg-center bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-gray-400">person</span>
                        </div>
                        <div class="flex flex-col overflow-hidden">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin User'); ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo htmlspecialchars($_SESSION['email'] ?? 'admin@platform.com'); ?></p>
                        </div>
                    </div>
                    <a href="logout.php" class="flex items-center gap-2 w-full px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        <span class="text-sm font-medium">Logout</span>
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
                    <label class="flex flex-col w-full max-w-md">
                        <div class="flex w-full items-stretch rounded-lg h-10">
                            <div
                                class="text-gray-400 flex items-center justify-center pl-4 bg-gray-100 dark:bg-gray-800 rounded-l-lg">
                                <span class="material-symbols-outlined text-[20px]">search</span>
                            </div>
                            <input
                                class="form-input w-full border-none bg-gray-100 dark:bg-gray-800 focus:ring-0 focus:outline-0 text-sm placeholder:text-gray-500 dark:text-white px-4 rounded-r-lg"
                                placeholder="Search resources, tags, or IDs..." value="" />
                        </div>
                    </label>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        class="flex items-center justify-center size-10 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-xl text-gray-700 dark:text-gray-300">dark_mode</span>
                    </button>
                    <button id="open-drawer-btn"
                        class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg font-bold text-sm tracking-wide hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span>Add New Resource</span>
                    </button>
                </div>
            </header>
            <!-- Dashboard Body -->
            <div class="p-8 space-y-8">
                <!-- Page Heading -->
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="space-y-1">
                        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Resource Management
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 text-base">Curate and moderate the platform's top
                            resource listings.</p>
                    </div>
                    <!-- Segmented Control -->
                    <div class="flex bg-gray-100 dark:bg-gray-800 p-1 rounded-xl w-fit">
                        <a href="websites.php"
                            class="px-6 py-2 bg-white dark:bg-gray-700 shadow-sm rounded-lg text-sm font-semibold text-gray-900 dark:text-white transition-all">
                            All Resources
                        </a>
                        <a href="categories.php"
                            class="px-6 py-2 rounded-lg text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-all">
                            Manage Categories
                        </a>
                    </div>
                </div>
                
                <?php if (!empty($message)): ?>
                    <div class="bg-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-50 dark:bg-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-900/20 border border-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-200 dark:border-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-800 rounded-xl p-4">
                        <p class="text-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-700 dark:text-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-400"><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Pending Reviews</p>
                            <span class="material-symbols-outlined text-amber-500">pending_actions</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $pendingReviews; ?></p>
                        <div class="mt-2 flex items-center text-xs text-amber-500 font-medium">
                            <span>Requires attention</span>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Active Categories</p>
                            <span class="material-symbols-outlined text-purple-500">category</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $totalCategories; ?></p>
                        <div class="mt-2 flex items-center text-xs text-gray-400 font-medium">
                            <span>Available categories</span>
                        </div>
                    </div>
                </div>
                
                    <!-- Data Table Container -->
                    <div
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Site Name</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Category</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Avg Rating</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Reviews</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php if (empty($websites)): ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                                No websites found. <a href="?action=create" class="text-primary hover:underline">Create one</a>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($websites as $web): ?>
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="size-8 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                                            <span class="material-symbols-outlined text-gray-400">language</span>
                                                        </div>
                                                        <span class="text-sm font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($web['name']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"><?php echo htmlspecialchars($web['category_name']); ?></span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-1.5">
                                                        <span
                                                            class="material-symbols-outlined text-amber-400 text-[18px] fill-current">star</span>
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo number_format($web['rating'], 1); ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?php echo $web['total_ratings']; ?></td>
                                                <td class="px-6 py-4">
                                                    <div
                                                        class="flex items-center gap-1.5 text-green-600 dark:text-green-400 font-semibold text-sm">
                                                        <div class="size-2 rounded-full bg-current"></div>
                                                        Published
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <a href="../website.php?id=<?php echo $web['id']; ?>" target="_blank"
                                                            class="p-1.5 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition-colors"
                                                            title="View Website">
                                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                                        </a>
                                                        <button onclick="editWebsite(<?php echo $web['id']; ?>)"
                                                            class="p-1.5 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition-colors"
                                                            title="Edit Resource">
                                                            <span class="material-symbols-outlined text-[20px]">edit_note</span>
                                                        </button>
                                                        <button onclick="deleteWebsite(<?php echo $web['id']; ?>, '<?php echo addslashes($web['name']); ?>')"
                                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                            title="Delete">
                                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Placeholder -->
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Showing 1 to <?php echo min(count($websites), 10); ?> of <?php echo count($websites); ?> resources</p>
                        </div>
                    </div>

                    <!-- Drawer -->
                    <div class="fixed inset-0 z-40 <?php echo $drawerOpen ? '' : 'pointer-events-none'; ?>" aria-hidden="<?php echo $drawerOpen ? 'false' : 'true'; ?>">
                        <div id="websites-drawer-backdrop"
                            class="absolute inset-0 bg-black/40 transition-opacity <?php echo $drawerOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'; ?>">
                        </div>
                        <section
                            class="fixed top-0 right-0 w-1/2 h-full bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-gray-800 shadow-2xl transition-transform duration-200 transform <?php echo $drawerOpen ? 'translate-x-0' : 'translate-x-full'; ?> flex flex-col overflow-y-auto"
                            role="dialog" aria-modal="true" aria-label="Resource drawer">
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                                <div class="space-y-1">
                                    <p class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold">Resource desk</p>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($drawerTitle); ?></h3>
                                </div>
                                <button id="close-drawer-btn"
                                    class="size-10 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-900 dark:text-gray-400 transition-colors">
                                    <span class="material-symbols-outlined">close</span>
                                </button>
                            </div>
                            <div class="px-6 py-6 space-y-6 flex-1">
                                <form class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                                        <select name="category_id" required
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>">
                                                    <?php echo htmlspecialchars($cat['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                                        <input type="text" name="name" required 
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">URL *</label>
                                        <input type="url" name="url" required 
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                        <textarea name="description" rows="5"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary"></textarea>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <button type="submit"
                                            class="flex-1 bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-600 transition-colors">
                                            Create Website
                                        </button>
                                        <button type="button" onclick="window.crudManager?.closeDrawer()"
                                            class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
            </div>
        </main>
    </div>
</body>

</html>
