<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Website.php';

$reviewModel = new Review();
$websiteModel = new Website();
$message = '';
$messageType = '';

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$websiteId = isset($_GET['website_id']) ? (int)$_GET['website_id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $websiteId = (int)$_POST['website_id'];
        $authorName = trim($_POST['author_name']);
        $authorEmail = trim($_POST['author_email']);
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        
        if (empty($authorName) || $rating < 1 || $rating > 5 || $websiteId <= 0) {
            $message = 'Name, valid rating, and website are required.';
            $messageType = 'error';
        } else {
            $result = $reviewModel->create($websiteId, $authorName, $authorEmail, $rating, $comment, $_SESSION['user_id']);
            if ($result) {
                $message = 'Review created successfully!';
                $messageType = 'success';
                $action = 'list';
            } else {
                $message = 'Failed to create review.';
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['update_status'])) {
        $id = (int)$_POST['id'];
        $newStatus = $_POST['status'];
        $websiteId = (int)($_POST['website_id'] ?? 0); // Keep variable for redirection
        
        $result = $reviewModel->updateStatus($id, $newStatus);
        if ($result) {
            $message = 'Review status updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to update review status.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $websiteId = (int)($_POST['website_id'] ?? 0); // Keep variable for redirection
        $result = $reviewModel->delete($id);
        if ($result) {
            $message = 'Review deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to delete review.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $authorName = trim($_POST['author_name']);
        $authorEmail = trim($_POST['author_email']);
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        $status = isset($_POST['status']) ? $_POST['status'] : null;
        $websiteId = (int)($_POST['website_id'] ?? 0); // Keep variable for redirection
        
        if (empty($authorName) || $rating < 1 || $rating > 5) {
            $message = 'Name and valid rating are required.';
            $messageType = 'error';
        } else {
            $result = $reviewModel->update($id, $authorName, $authorEmail, $rating, $comment, $status);
            if ($result) {
                $message = 'Review updated successfully!';
                $messageType = 'success';
                $action = 'list';
            } else {
                $message = 'Failed to update review.';
                $messageType = 'error';
            }
        }
    }
}

// Fetch Reviews
if ($websiteId > 0) {
    // If filtering by website, get only those reviews
    // Note: getByWebsite includes approved check by default, we need all statuses for admin
    // We'll use getAll and filter manually or add a new method. For now, filter manually from getAll for simplicity
    // unless performance becomes an issue. Actually, getAll queries everything.
    $allReviews = $reviewModel->getAll();
    $reviews = array_filter($allReviews, function($r) use ($websiteId) {
        return $r['website_id'] == $websiteId;
    });
    $website = $websiteModel->getById($websiteId);
    $pageTitle = 'Reviews for ' . htmlspecialchars($website['name']);
} else {
    $reviews = $reviewModel->getAll();
    $pageTitle = 'All Reviews';
}

// Filter by status if set
if ($status !== 'all') {
    $reviews = array_filter($reviews, function($r) use ($status) {
        return $r['status'] === $status;
    });
}

// Stats
$totalReviews = count($reviews);
$pendingReviews = count(array_filter($reviews, function($r) { return $r['status'] === 'pending'; }));
$avgRating = 0;
if ($totalReviews > 0) {
    $sum = array_reduce($reviews, function($carry, $item) { return $carry + $item['rating']; }, 0);
    $avgRating = $sum / $totalReviews;
}

$review = null;
if ($action === 'edit' && $id > 0) {
    $review = $reviewModel->getById($id);
    if (!$review) {
        $action = 'list';
        $message = 'Review not found.';
        $messageType = 'error';
    }
}

$drawerOpen = in_array($action, ['create', 'edit']);
$drawerTitle = $action === 'edit' ? 'Edit Review' : 'Create New Review';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Reviews Management - Admin Portal</title>
    <!-- Google Fonts & Material Icons -->
    <link href="../public/assets/css/lexend.css" rel="stylesheet" />
    <link href="../public/assets/css/material-symbols.css" rel="stylesheet" />
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
                    fontFamily: { "display": ["Lexend"] },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>body { font-family: 'Lexend', sans-serif; }</style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-white dark:bg-background-dark border-r border-gray-200 dark:border-gray-800 flex flex-col">
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
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" href="websites.php">
                        <span class="material-symbols-outlined text-[24px]">database</span>
                        <p class="text-sm font-medium">Resources</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" href="categories.php">
                        <span class="material-symbols-outlined text-[24px]">folder</span>
                        <p class="text-sm font-medium">Categories</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary" href="reviews.php">
                        <span class="material-symbols-outlined text-[24px]">rate_review</span>
                        <p class="text-sm font-semibold">Reviews</p>
                    </a>
               
                    <div class="my-4 border-t border-gray-100 dark:border-gray-800"></div>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" href="index.php">
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
            <header class="flex items-center justify-between bg-white dark:bg-background-dark border-b border-gray-200 dark:border-gray-800 px-8 py-4 sticky top-0 z-10">
                <div class="flex items-center gap-6 flex-1">
                    <label class="flex flex-col w-full max-w-md">
                        <div class="flex w-full items-stretch rounded-lg h-10">
                            <div class="text-gray-400 flex items-center justify-center pl-4 bg-gray-100 dark:bg-gray-800 rounded-l-lg">
                                <span class="material-symbols-outlined text-[20px]">search</span>
                            </div>
                            <input class="form-input w-full border-none bg-gray-100 dark:bg-gray-800 focus:ring-0 focus:outline-0 text-sm placeholder:text-gray-500 dark:text-white px-4 rounded-r-lg" placeholder="Search reviews..." />
                        </div>
                    </label>
                </div>
                <div class="flex items-center gap-3">
                    <button class="flex items-center justify-center size-10 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-xl text-gray-700 dark:text-gray-300">dark_mode</span>
                    </button>
                    <a href="?action=create<?php echo $websiteId ? '&website_id=' . $websiteId : ''; ?>" class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg font-bold text-sm tracking-wide hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span>Create Review</span>
                    </a>
                </div>
            </header>

            <!-- Dashboard Body -->
            <div class="p-8 space-y-8">
                <!-- Page Heading -->
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="space-y-1">
                        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight"><?php echo htmlspecialchars($pageTitle); ?></h2>
                        <p class="text-gray-500 dark:text-gray-400 text-base">Moderate user feedback and ratings.</p>
                    </div>
                    <?php if ($websiteId > 0): ?>
                        <a href="websites.php" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-sm font-semibold text-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                            ← Back to Websites
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="bg-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-50 dark:bg-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-900/20 border border-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-200 dark:border-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-800 rounded-xl p-4">
                    <p class="text-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-700 dark:text-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-400"><?php echo htmlspecialchars($message); ?></p>
                </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="flex gap-4">
                    <?php
                        $baseUrl = '?';
                        if ($websiteId) $baseUrl .= 'website_id=' . $websiteId . '&';
                    ?>
                    <a href="<?php echo $baseUrl; ?>status=all" class="px-4 py-2 rounded-lg text-sm font-bold <?php echo $status == 'all' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700'; ?>">All</a>
                    <a href="<?php echo $baseUrl; ?>status=pending" class="px-4 py-2 rounded-lg text-sm font-bold <?php echo $status == 'pending' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700'; ?>">Pending</a>
                    <a href="<?php echo $baseUrl; ?>status=approved" class="px-4 py-2 rounded-lg text-sm font-bold <?php echo $status == 'approved' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700'; ?>">Approved</a>
                    <a href="<?php echo $baseUrl; ?>status=rejected" class="px-4 py-2 rounded-lg text-sm font-bold <?php echo $status == 'rejected' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700'; ?>">Rejected</a>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                             <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Reviews</p>
                             <span class="material-symbols-outlined text-primary">rate_review</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $totalReviews; ?></p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                             <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Pending</p>
                             <span class="material-symbols-outlined text-amber-500">pending_actions</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $pendingReviews; ?></p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                             <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Avg Rating</p>
                             <span class="material-symbols-outlined text-yellow-500">star</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo number_format($avgRating, 1); ?>/5</p>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Website</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Author</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rating</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Comment</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php if (empty($reviews)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No reviews found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reviews as $rev): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="../website.php?id=<?php echo $rev['website_id']; ?>" target="_blank" class="hover:text-primary">
                                                <?php echo htmlspecialchars($rev['website_name']); ?>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($rev['author_name']); ?></span>
                                                <span class="text-xs text-gray-500"><?php echo htmlspecialchars($rev['author_email']); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-1">
                                                <span class="text-sm font-bold"><?php echo $rev['rating']; ?></span>
                                                <span class="material-symbols-outlined text-yellow-400 text-[16px] fill-current">star</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            <?php echo htmlspecialchars(substr($rev['comment'], 0, 50)) . (strlen($rev['comment']) > 50 ? '...' : ''); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php
                                                $statusColor = 'yellow';
                                                if ($rev['status'] === 'approved') $statusColor = 'green';
                                                if ($rev['status'] === 'rejected') $statusColor = 'red';
                                            ?>
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-700 dark:bg-<?php echo $statusColor; ?>-900/40 dark:text-<?php echo $statusColor; ?>-300">
                                                <?php echo ucfirst($rev['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                 <form method="POST" class="inline-block">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <input type="hidden" name="id" value="<?php echo $rev['id']; ?>">
                                                    <input type="hidden" name="website_id" value="<?php echo $websiteId; ?>">
                                                    <select name="status" onchange="this.form.submit()" class="text-xs border-none bg-gray-100 dark:bg-gray-700 rounded p-1 cursor-pointer">
                                                        <option value="pending" <?php echo $rev['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="approved" <?php echo $rev['status'] === 'approved' ? 'selected' : ''; ?>>Approve</option>
                                                        <option value="rejected" <?php echo $rev['status'] === 'rejected' ? 'selected' : ''; ?>>Reject</option>
                                                    </select>
                                                </form>
                                                <a href="?action=edit&id=<?php echo $rev['id']; ?><?php echo $websiteId ? '&website_id=' . $websiteId : ''; ?>" class="p-1.5 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition-colors">
                                                    <span class="material-symbols-outlined text-[20px]">edit_note</span>
                                                </a>
                                                <form method="POST" class="inline-block" onsubmit="return confirm('Delete review?');">
                                                    <input type="hidden" name="delete" value="1">
                                                    <input type="hidden" name="id" value="<?php echo $rev['id']; ?>">
                                                    <input type="hidden" name="website_id" value="<?php echo $websiteId; ?>">
                                                    <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Drawer -->
                <div class="fixed inset-0 z-40 <?php echo $drawerOpen ? '' : 'pointer-events-none'; ?>" aria-hidden="<?php echo $drawerOpen ? 'false' : 'true'; ?>">
                    <div id="drawer-backdrop" class="absolute inset-0 bg-black/40 transition-opacity <?php echo $drawerOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'; ?>"></div>
                    <section class="fixed top-0 right-0 w-1/2 h-full bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-gray-800 shadow-2xl transition-transform duration-200 transform <?php echo $drawerOpen ? 'translate-x-0' : 'translate-x-full'; ?> flex flex-col overflow-y-auto">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($drawerTitle); ?></h3>
                            <a href="reviews.php<?php echo $websiteId ? '?website_id=' . $websiteId : ''; ?>" class="size-10 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-900 dark:text-gray-400 transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </a>
                        </div>
                        <div class="px-6 py-6 flex-1">
                            <form method="POST" class="space-y-6">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="update" value="1">
                                    <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                <?php else: ?>
                                    <input type="hidden" name="create" value="1">
                                <?php endif; ?>
                                <input type="hidden" name="website_id" value="<?php echo $websiteId; ?>">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website</label>
                                    <select name="website_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white" required <?php echo ($action === 'edit' || $websiteId > 0) ? '' : ''; ?>>
                                        <option value="">Select Website</option>
                                        <?php
                                        $allWebsites = $websiteModel->getAll();
                                        foreach ($allWebsites as $web):
                                            $selected = ($review && $review['website_id'] == $web['id']) || ($websiteId == $web['id']);
                                        ?>
                                        <option value="<?php echo $web['id']; ?>" <?php echo $selected ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($web['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Author Name</label>
                                    <input type="text" name="author_name" required value="<?php echo $review ? htmlspecialchars($review['author_name']) : ''; ?>" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Author Email</label>
                                    <input type="email" name="author_email" value="<?php echo $review ? htmlspecialchars($review['author_email']) : ''; ?>" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                                    <select name="rating" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($review && $review['rating'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?> Stars</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment</label>
                                    <textarea name="comment" rows="4" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white"><?php echo $review ? htmlspecialchars($review['comment']) : ''; ?></textarea>
                                </div>
                                
                                <?php if ($action === 'edit'): ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white">
                                        <option value="pending" <?php echo $review['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="approved" <?php echo $review['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="rejected" <?php echo $review['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </div>
                                <?php endif; ?>

                                <div class="flex gap-4">
                                    <button type="submit" class="flex-1 bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-600 transition-colors">
                                        <?php echo $action === 'edit' ? 'Update' : 'Create'; ?> Review
                                    </button>
                                     <a href="reviews.php<?php echo $websiteId ? '?website_id=' . $websiteId : ''; ?>" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>

            </div>
        </main>
    </div>
    <!-- Custom JS for Dark Mode -->
    <script>
        // Check local storage for theme
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
        
        document.getElementById('theme-toggle').addEventListener('click', function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });
    </script>
</body>
</html>

