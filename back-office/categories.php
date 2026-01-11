<?php
require_once __DIR__ . '/../models/Category.php';

$categoryModel = new Category();
$message = '';
$messageType = '';

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $description = trim($_POST['description']);
        
        if (empty($name) || empty($slug)) {
            $message = 'Name and slug are required.';
            $messageType = 'error';
        } else {
            $result = $categoryModel->create($name, $slug, $description);
            if ($result) {
                $message = 'Category created successfully!';
                $messageType = 'success';
                $action = 'list';
            } else {
                $message = 'Failed to create category. Slug might already exist.';
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $description = trim($_POST['description']);
        
        if (empty($name) || empty($slug)) {
            $message = 'Name and slug are required.';
            $messageType = 'error';
        } else {
            $result = $categoryModel->update($id, $name, $slug, $description);
            if ($result) {
                $message = 'Category updated successfully!';
                $messageType = 'success';
                $action = 'list';
            } else {
                $message = 'Failed to update category.';
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $result = $categoryModel->delete($id);
        if ($result) {
            $message = 'Category deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to delete category.';
            $messageType = 'error';
        }
        $action = 'list';
    }
}

$categories = $categoryModel->getAll();
$category = null;

if ($action === 'edit' && $id > 0) {
    $category = $categoryModel->getById($id);
    if (!$category) {
        $action = 'list';
        $message = 'Category not found.';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Categories</title>
</head>
<body>
    <h1>Back Office - Categories</h1>
    
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
        <h2>All Categories</h2>
        <p><a href="?action=create">Create New Category</a></p>
        
        <?php if (empty($categories)): ?>
            <p>No categories found.</p>
        <?php else: ?>
            <table border="1" cellpadding="10">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                        <td><?php echo htmlspecialchars($cat['description']); ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $cat['id']; ?>">Edit</a> |
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                <input type="hidden" name="delete" value="1">
                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        
    <?php elseif ($action === 'create' || $action === 'edit'): ?>
        <h2><?php echo $action === 'create' ? 'Create' : 'Edit'; ?> Category</h2>
        <p><a href="categories.php">← Back to List</a></p>
        
        <form method="POST">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="update" value="1">
                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
            <?php else: ?>
                <input type="hidden" name="create" value="1">
            <?php endif; ?>
            
            <label>Name: *</label><br>
            <input type="text" name="name" value="<?php echo $category ? htmlspecialchars($category['name']) : ''; ?>" required><br><br>
            
            <label>Slug: *</label><br>
            <input type="text" name="slug" value="<?php echo $category ? htmlspecialchars($category['slug']) : ''; ?>" required><br><br>
            
            <label>Description:</label><br>
            <textarea name="description" rows="5" cols="50"><?php echo $category ? htmlspecialchars($category['description']) : ''; ?></textarea><br><br>
            
            <button type="submit"><?php echo $action === 'create' ? 'Create' : 'Update'; ?> Category</button>
        </form>
    <?php endif; ?>
</body>
</html>

