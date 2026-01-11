<?php
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Website.php';

$categorySlug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($categorySlug)) {
    header('Location: index.php');
    exit;
}

$categoryModel = new Category();
$category = $categoryModel->getBySlug($categorySlug);

if (!$category) {
    header('Location: index.php');
    exit;
}

$websiteModel = new Website();
$websites = $websiteModel->getByCategory($category['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - Websites</title>
</head>
<body>
    <a href="index.php">← Back to Categories</a>
    
    <h1><?php echo htmlspecialchars($category['name']); ?></h1>
    
    <?php if (!empty($category['description'])): ?>
        <p><?php echo htmlspecialchars($category['description']); ?></p>
    <?php endif; ?>
    
    <h2>Best Leaders (Websites)</h2>
    
    <?php if (empty($websites)): ?>
        <p>No websites found in this category.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($websites as $website): ?>
                <li>
                    <h3>
                        <a href="website.php?id=<?php echo $website['id']; ?>">
                            <?php echo htmlspecialchars($website['name']); ?>
                        </a>
                    </h3>
                    <p>
                        <strong>Rating:</strong> <?php echo number_format($website['rating'], 2); ?>/5.00 
                        (<?php echo $website['total_ratings']; ?> ratings)
                    </p>
                    <p>
                        <strong>URL:</strong> 
                        <a href="<?php echo htmlspecialchars($website['url']); ?>" target="_blank">
                            <?php echo htmlspecialchars($website['url']); ?>
                        </a>
                    </p>
                    <?php if (!empty($website['description'])): ?>
                        <p><?php echo htmlspecialchars($website['description']); ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>

