<?php
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
    if (isset($_POST['update_status'])) {
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Reviews</title>
</head>
<body>
    <h1>Back Office - Reviews</h1>
    
    <nav>
        <a href="index.php">Dashboard</a> |
        <a href="categories.php">Categories</a> |
        <a href="websites.php">Websites</a> |
        <a href="reviews.php">Reviews</a> |
        <a href="../index.php">Front Office</a>
    </nav>
    
    <hr>
    
    <?php if (!empty($message)): ?>
        <p style="color: <?php echo $messageType === 'error' ? 'red' : 'green'; ?>;">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>
    
    <?php if ($action === 'list'): ?>
        <h2>All Reviews</h2>
        
        <p>
            Filter by status: 
            <a href="?status=all">All</a> |
            <a href="?status=pending">Pending</a> |
            <a href="?status=approved">Approved</a> |
            <a href="?status=rejected">Rejected</a>
        </p>
        
        <?php if (empty($reviews)): ?>
            <p>No reviews found.</p>
        <?php else: ?>
            <table border="1" cellpadding="10">
                <tr>
                    <th>ID</th>
                    <th>Website</th>
                    <th>Author</th>
                    <th>Email</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
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
        
    <?php elseif ($action === 'edit'): ?>
        <h2>Edit Review</h2>
        <p><a href="reviews.php">← Back to List</a></p>
        
        <form method="POST">
            <input type="hidden" name="update" value="1">
            <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
            
            <p><strong>Website:</strong> <?php echo htmlspecialchars($review['website_name']); ?></p>
            
            <label>Author Name: *</label><br>
            <input type="text" name="author_name" value="<?php echo htmlspecialchars($review['author_name']); ?>" required><br><br>
            
            <label>Author Email:</label><br>
            <input type="email" name="author_email" value="<?php echo htmlspecialchars($review['author_email']); ?>"><br><br>
            
            <label>Rating: *</label><br>
            <select name="rating" required>
                <option value="1" <?php echo $review['rating'] == 1 ? 'selected' : ''; ?>>1 - Poor</option>
                <option value="2" <?php echo $review['rating'] == 2 ? 'selected' : ''; ?>>2 - Fair</option>
                <option value="3" <?php echo $review['rating'] == 3 ? 'selected' : ''; ?>>3 - Good</option>
                <option value="4" <?php echo $review['rating'] == 4 ? 'selected' : ''; ?>>4 - Very Good</option>
                <option value="5" <?php echo $review['rating'] == 5 ? 'selected' : ''; ?>>5 - Excellent</option>
            </select><br><br>
            
            <label>Comment:</label><br>
            <textarea name="comment" rows="5" cols="50"><?php echo htmlspecialchars($review['comment']); ?></textarea><br><br>
            
            <label>Status:</label><br>
            <select name="status">
                <option value="pending" <?php echo $review['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo $review['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="rejected" <?php echo $review['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select><br><br>
            
            <button type="submit">Update Review</button>
        </form>
    <?php endif; ?>
</body>
</html>

