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
            $result = $reviewModel->create($websiteId, $authorName, $authorEmail, $rating, $comment);
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

$allReviews = $reviewModel->getAll();

// Filter by status
if ($status !== 'all') {
    $reviews = array_filter($allReviews, function($r) use ($status) {
        return $r['status'] === $status;
    });
} else {
    $reviews = $allReviews;
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
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Reviews</title>
    <link href="../public/assets/css/lexend.css" rel="stylesheet" />
    <link href="../public/assets/css/material-symbols.css" rel="stylesheet" />
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
                },
            },
        }
    </script>
    <!-- AJAX API Client -->
    <script src="../public/assets/js/api-client.js"></script>
    <!-- Custom JS -->
    <script src="../public/assets/js/custom.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-white min-h-screen p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold">Back Office - Reviews</h1>
            <!-- Dark Mode Toggle -->
            <button id="theme-toggle" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                <span class="material-symbols-outlined text-xl">dark_mode</span>
            </button>
        </div>
        
        <nav class="mb-8">
            <a href="index.php" class="text-primary hover:underline mr-4">Dashboard</a> |
            <a href="categories.php" class="text-primary hover:underline mr-4">Categories</a> |
            <a href="websites.php" class="text-primary hover:underline mr-4">Websites</a> |
            <a href="reviews.php" class="text-primary hover:underline mr-4">Reviews</a> |
            <a href="../index.php" class="text-primary hover:underline mr-4">Front Office</a> |
            <a href="logout.php" class="text-red-600 hover:underline">Logout</a>
        </nav>
    
<hr class="border-gray-300 dark:border-gray-600 mb-8">
        
        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400' : 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
    <?php endif; ?>
    
    <?php if ($action === 'list'): ?>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">All Reviews</h2>
            <a href="?action=create" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">Create Review</a>
        </div>
        
        <div class="mb-6">
            <p class="mb-2">Filter by status:</p>
            <div class="flex gap-4">
                <a href="?status=all" class="px-3 py-1 rounded <?php echo $status === 'all' ? 'bg-primary text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'; ?> hover:bg-primary hover:text-white transition-colors">All</a>
                <a href="?status=pending" class="px-3 py-1 rounded <?php echo $status === 'pending' ? 'bg-primary text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'; ?> hover:bg-primary hover:text-white transition-colors">Pending</a>
                <a href="?status=approved" class="px-3 py-1 rounded <?php echo $status === 'approved' ? 'bg-primary text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'; ?> hover:bg-primary hover:text-white transition-colors">Approved</a>
                <a href="?status=rejected" class="px-3 py-1 rounded <?php echo $status === 'rejected' ? 'bg-primary text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'; ?> hover:bg-primary hover:text-white transition-colors">Rejected</a>
            </div>
        </div>
        
        <?php if (empty($reviews)): ?>
            <p class="text-gray-500 dark:text-gray-400">No reviews found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Website</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Author</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rating</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Comment</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        <?php foreach ($reviews as $rev): ?>
                            <tr>
                        <td><?php echo $rev['id']; ?></td>
                        <td>
                            <a href="../website.php?id=<?php echo $rev['website_id']; ?>" target="_blank">
                                <?php echo htmlspecialchars($rev['website_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($rev['author_name']); ?></td>
                        <td><?php echo htmlspecialchars($rev['author_email']); ?></td>
                        <td><?php echo $rev['rating']; ?>/5</td>
                        <td><?php echo htmlspecialchars(substr($rev['comment'], 0, 50)); ?><?php echo strlen($rev['comment']) > 50 ? '...' : ''; ?></td>
                        <td>
                            <strong style="color: 
                                <?php 
                                echo $rev['status'] === 'approved' ? 'green' : 
                                    ($rev['status'] === 'rejected' ? 'red' : 'orange'); 
                                ?>;">
                                <?php echo strtoupper($rev['status']); ?>
                            </strong>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($rev['created_at'])); ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $rev['id']; ?>">Edit</a> |
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="update_status" value="1">
                                <input type="hidden" name="id" value="<?php echo $rev['id']; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $rev['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo $rev['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="rejected" <?php echo $rev['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </form> |
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                <input type="hidden" name="delete" value="1">
                                <input type="hidden" name="id" value="<?php echo $rev['id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        
    <?php elseif ($action === 'create'): ?>
        <h2 class="text-2xl font-bold mb-4">Create Review</h2>
        <p class="mb-4"><a href="reviews.php" class="text-primary hover:underline">← Back to List</a></p>
        
        <form method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-300 dark:border-gray-600">
            <input type="hidden" name="create" value="1">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website: *</label>
                <select name="website_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                    <option value="">Select a website</option>
                    <?php
                    $websites = $websiteModel->getAll();
                    foreach ($websites as $website): ?>
                        <option value="<?php echo $website['id']; ?>"><?php echo htmlspecialchars($website['name']); ?> (<?php echo htmlspecialchars($website['url']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Author Name: *</label>
                <input type="text" name="author_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Author Email:</label>
                <input type="email" name="author_email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating: *</label>
                <select name="rating" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                    <option value="1">1 - Poor</option>
                    <option value="2">2 - Fair</option>
                    <option value="3" selected>3 - Good</option>
                    <option value="4">4 - Very Good</option>
                    <option value="5">5 - Excellent</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment:</label>
                <textarea name="comment" rows="5" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
            
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">Create Review</button>
        </form>
        
    <?php elseif ($action === 'edit'): ?>
        <h2 class="text-2xl font-bold mb-4">Edit Review</h2>
        <p class="mb-4"><a href="reviews.php" class="text-primary hover:underline">← Back to List</a></p>
        
        <form method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-300 dark:border-gray-600">
            <input type="hidden" name="update" value="1">
            <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website:</label>
                <p class="text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg"><?php echo htmlspecialchars($review['website_name']); ?></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Author Name: *</label>
                <input type="text" name="author_name" value="<?php echo htmlspecialchars($review['author_name']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Author Email:</label>
                <input type="email" name="author_email" value="<?php echo htmlspecialchars($review['author_email']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating: *</label>
                <select name="rating" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                    <option value="1" <?php echo $review['rating'] == 1 ? 'selected' : ''; ?>>1 - Poor</option>
                    <option value="2" <?php echo $review['rating'] == 2 ? 'selected' : ''; ?>>2 - Fair</option>
                    <option value="3" <?php echo $review['rating'] == 3 ? 'selected' : ''; ?>>3 - Good</option>
                    <option value="4" <?php echo $review['rating'] == 4 ? 'selected' : ''; ?>>4 - Very Good</option>
                    <option value="5" <?php echo $review['rating'] == 5 ? 'selected' : ''; ?>>5 - Excellent</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment:</label>
                <textarea name="comment" rows="5" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?php echo htmlspecialchars($review['comment']); ?></textarea>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status:</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="pending" <?php echo $review['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $review['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $review['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">Update Review</button>
        </form>
    <?php endif; ?>
</body>
</html>

