<?php
require_once __DIR__ . '/models/Website.php';
require_once __DIR__ . '/models/Review.php';

$websiteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($websiteId <= 0) {
    header('Location: index.php');
    exit;
}

$websiteModel = new Website();
$website = $websiteModel->getById($websiteId);

if (!$website) {
    header('Location: index.php');
    exit;
}

$reviewModel = new Review();
$reviews = $reviewModel->getByWebsite($websiteId, 'approved');

// Handle review submission
$reviewSubmitted = false;
$reviewError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $authorName = isset($_POST['author_name']) ? trim($_POST['author_name']) : '';
    $authorEmail = isset($_POST['author_email']) ? trim($_POST['author_email']) : '';
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    
    if (empty($authorName) || $rating < 1 || $rating > 5) {
        $reviewError = 'Please fill in all required fields with valid data.';
    } else {
        $result = $reviewModel->create($websiteId, $authorName, $authorEmail, $rating, $comment);
        if ($result) {
            $reviewSubmitted = true;
            $websiteModel->updateRating($websiteId);
            $website = $websiteModel->getById($websiteId);
            $reviews = $reviewModel->getByWebsite($websiteId, 'approved');
        } else {
            $reviewError = 'Failed to submit review. Please try again.';
        }
    }
}

// Helper functions
function getStarRating($rating) {
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    
    $html = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<span class="material-symbols-outlined fill-icon text-xl">star</span>';
    }
    if ($hasHalfStar) {
        $html .= '<span class="material-symbols-outlined text-xl">star_half</span>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<span class="material-symbols-outlined text-xl">star</span>';
    }
    return $html;
}

function getStarRatingSmall($rating) {
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    
    $html = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<span class="material-symbols-outlined fill-icon text-sm">star</span>';
    }
    if ($hasHalfStar) {
        $html .= '<span class="material-symbols-outlined text-sm">star_half</span>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<span class="material-symbols-outlined text-sm">star</span>';
    }
    return $html;
}

function formatReviewCount($count) {
    if ($count >= 1000) {
        return number_format($count / 1000, 1) . 'k';
    }
    return $count;
}
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo htmlspecialchars($website['name']); ?> - ResourceHub</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
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
    <script src="public/assets/js/custom.js"></script>
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .fill-icon {
            font-variation-settings: 'FILL' 1;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen">
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <!-- Top Navigation Bar -->
            <header
                class="flex items-center justify-between whitespace-nowrap border-b border-solid border-gray-200 dark:border-gray-800 bg-white dark:bg-background-dark px-10 py-3 sticky top-0 z-50">
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-4 text-primary">
                        <div class="size-6">
                            <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z"
                                    fill="currentColor"></path>
                            </svg>
                        </div>
                        <a href="index.php"
                            class="text-gray-900 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em] font-display">
                            ResourceHub</a>
                    </div>
                    <nav class="flex items-center gap-9">
                        <a class="text-gray-700 dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary transition-colors"
                            href="index.php">Categories</a>
                        <a class="text-gray-700 dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary transition-colors"
                            href="index.php">Top Rated</a>
                        <a class="text-gray-700 dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary transition-colors"
                            href="index.php">Community</a>
                        <a class="text-gray-700 dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary transition-colors"
                            href="index.php">About</a>
                    </nav>
                </div>
                <div class="flex flex-1 justify-end gap-6 items-center">
                    <label class="flex flex-col min-w-40 h-10 max-w-64">
                        <div class="flex w-full flex-1 items-stretch rounded-lg h-full">
                            <div
                                class="text-gray-500 flex border-none bg-gray-100 dark:bg-gray-800 items-center justify-center pl-4 rounded-l-lg">
                                <span class="material-symbols-outlined text-xl">search</span>
                            </div>
                            <input
                                class="form-input flex w-full min-w-0 flex-1 border-none bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-0 focus:outline-0 h-full placeholder:text-gray-500 px-4 rounded-r-lg text-sm font-normal"
                                placeholder="Search resources..." />
                        </div>
                    </label>
                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-xl text-gray-700 dark:text-gray-300">dark_mode</span>
                    </button>
                    <a href="back-office/index.php"
                        class="flex min-w-[84px] cursor-pointer items-center justify-center rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold">
                        <span>Sign Up</span>
                    </a>
                </div>
            </header>
            <main class="flex flex-1 justify-center py-8">
                <div class="layout-content-container flex flex-col max-w-[1200px] w-full px-6 gap-6">
                    <!-- Breadcrumbs -->
                    <div class="flex flex-wrap items-center gap-2">
                        <a class="text-gray-500 dark:text-gray-400 text-sm font-medium hover:text-primary"
                            href="index.php">Home</a>
                        <span class="material-symbols-outlined text-sm text-gray-400">chevron_right</span>
                        <a class="text-gray-500 dark:text-gray-400 text-sm font-medium hover:text-primary"
                            href="index.php?category=<?php echo $website['category_id']; ?>"><?php echo htmlspecialchars($website['category_name']); ?></a>
                        <span class="material-symbols-outlined text-sm text-gray-400">chevron_right</span>
                        <span class="text-gray-900 dark:text-white text-sm font-medium"><?php echo htmlspecialchars($website['name']); ?></span>
                    </div>
                    <!-- Profile Header -->
                    <div
                        class="bg-white dark:bg-background-dark rounded-xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
                        <div class="flex @container">
                            <div
                                class="flex w-full flex-col gap-6 @[640px]:flex-row @[640px]:justify-between items-start">
                                <div class="flex gap-6 items-center">
                                    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-xl min-h-32 w-32 border border-gray-100 dark:border-gray-700 bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-6xl text-primary/30">language</span>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-3">
                                            <h1 class="text-gray-900 dark:text-white text-3xl font-bold tracking-tight">
                                                <?php echo htmlspecialchars($website['name']); ?></h1>
                                            <?php if ($website['rating'] >= 4.5): ?>
                                                <span
                                                    class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">Verified</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400 text-lg"><?php echo htmlspecialchars($website['description'] ?: 'No description available.'); ?></p>
                                        <div class="flex items-center gap-4 mt-2">
                                            <div class="flex items-center text-orange-400">
                                                <?php echo getStarRating($website['rating']); ?>
                                                <span class="text-gray-900 dark:text-white font-bold ml-2"><?php echo number_format($website['rating'], 1); ?></span>
                                                <span
                                                    class="text-gray-500 dark:text-gray-400 font-normal text-sm ml-1">(<?php echo formatReviewCount($website['total_ratings']); ?>
                                                    reviews)</span>
                                            </div>
                                            <span class="text-gray-300">|</span>
                                            <p
                                                class="text-gray-600 dark:text-gray-400 text-sm font-medium flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">category</span> <?php echo htmlspecialchars($website['category_name']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?php echo htmlspecialchars($website['url']); ?>" target="_blank"
                                    class="flex min-w-[160px] cursor-pointer items-center justify-center rounded-lg h-12 px-6 bg-primary text-white text-base font-bold transition-transform active:scale-95 shadow-lg shadow-primary/20">
                                    <span class="material-symbols-outlined mr-2">open_in_new</span>
                                    <span>Visit Website</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Main Grid Content -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column: Details & Reviews -->
                        <div class="lg:col-span-2 flex flex-col gap-8">
                            <!-- Tabs -->
                            <div
                                class="bg-white dark:bg-background-dark rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-sm">
                                <div class="flex border-b border-gray-200 dark:border-gray-800 px-6 gap-8">
                                    <a class="flex flex-col items-center justify-center border-b-[3px] border-primary text-primary pb-[13px] pt-4"
                                        href="#overview">
                                        <p class="text-sm font-bold">Overview</p>
                                    </a>
                                    <a class="flex flex-col items-center justify-center border-b-[3px] border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 pb-[13px] pt-4 transition-colors"
                                        href="#reviews">
                                        <p class="text-sm font-bold">Reviews</p>
                                    </a>
                                </div>
                                <div class="p-6 flex flex-col gap-6">
                                    <div class="flex flex-col gap-4">
                                        <h3 class="text-gray-900 dark:text-white text-xl font-bold">About the Platform
                                        </h3>
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                            <?php echo nl2br(htmlspecialchars($website['description'] ?: 'No description available for this website.')); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Reviews Section -->
                            <div class="flex flex-col gap-6" id="reviews">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-gray-900 dark:text-white text-2xl font-bold">User Reviews</h3>
                                    <button onclick="document.getElementById('review-form').scrollIntoView({behavior: 'smooth'})"
                                        class="flex items-center gap-2 px-4 py-2 rounded-lg border border-primary text-primary font-bold hover:bg-primary/5 transition-colors">
                                        <span class="material-symbols-outlined">edit</span>
                                        Write a Review
                                    </button>
                                </div>
                                
                                <?php if ($reviewSubmitted): ?>
                                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                                        <p class="text-green-700 dark:text-green-400">Thank you! Your review has been submitted and is pending approval.</p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($reviewError)): ?>
                                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                                        <p class="text-red-700 dark:text-red-400"><?php echo htmlspecialchars($reviewError); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Review Form -->
                                <div id="review-form" class="bg-white dark:bg-background-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                                    <h4 class="text-gray-900 dark:text-white text-lg font-bold mb-4">Write a Review</h4>
                                    <form method="POST" action="">
                                        <input type="hidden" name="submit_review" value="1">
                                        
                                        <div class="flex flex-col gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Name *</label>
                                                <input type="text" name="author_name" required
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Email</label>
                                                <input type="email" name="author_email"
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating *</label>
                                                <select name="rating" required
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                                                    <option value="">Select rating</option>
                                                    <option value="5">5 - Excellent</option>
                                                    <option value="4">4 - Very Good</option>
                                                    <option value="3">3 - Good</option>
                                                    <option value="2">2 - Fair</option>
                                                    <option value="1">1 - Poor</option>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment</label>
                                                <textarea name="comment" rows="5"
                                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary"></textarea>
                                            </div>
                                            
                                            <button type="submit"
                                                class="w-full bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-600 transition-colors">
                                                Submit Review
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Review Cards -->
                                <div class="space-y-4">
                                    <?php if (empty($reviews)): ?>
                                        <div class="bg-white dark:bg-background-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm text-center">
                                            <p class="text-gray-500 dark:text-gray-400">No reviews yet. Be the first to review!</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach (array_slice($reviews, 0, 5) as $review): ?>
                                            <div
                                                class="bg-white dark:bg-background-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-4">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex gap-4 items-center">
                                                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 bg-center bg-cover border border-gray-200 flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-gray-400">person</span>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-900 dark:text-white font-bold"><?php echo htmlspecialchars($review['author_name']); ?></p>
                                                            <p class="text-gray-500 text-xs">Reviewed on <?php echo date('M d, Y', strtotime($review['created_at'])); ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="flex text-orange-400">
                                                        <?php
                                                        $rating = (int)$review['rating'];
                                                        for ($i = 0; $i < 5; $i++) {
                                                            if ($i < $rating) {
                                                                echo '<span class="material-symbols-outlined fill-icon text-sm">star</span>';
                                                            } else {
                                                                echo '<span class="material-symbols-outlined text-sm">star</span>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php if (!empty($review['comment'])): ?>
                                                    <p class="text-gray-700 dark:text-gray-300">
                                                        <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                                    </p>
                                                <?php endif; ?>
                                                <div
                                                    class="flex items-center gap-6 pt-2 border-t border-gray-100 dark:border-gray-800">
                                                    <button
                                                        class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary transition-colors">
                                                        <span class="material-symbols-outlined text-lg">thumb_up</span>
                                                        Helpful (0)
                                                    </button>
                                                    <button
                                                        class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-red-500 transition-colors">
                                                        <span class="material-symbols-outlined text-lg">flag</span>
                                                        Report
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <?php if (count($reviews) > 5): ?>
                                            <button class="w-full py-3 text-primary font-bold text-sm hover:underline">Show <?php echo count($reviews) - 5; ?> more reviews</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Right Column: Sidebar -->
                        <div class="lg:col-span-1 flex flex-col gap-6">
                            <!-- Quick Stats -->
                            <div
                                class="bg-white dark:bg-background-dark rounded-xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
                                <h3 class="text-gray-900 dark:text-white text-lg font-bold mb-4">Quick Stats</h3>
                                <div class="space-y-4">
                                    <div
                                        class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                                        <span class="text-gray-500 text-sm">Rating</span>
                                        <span class="text-gray-900 dark:text-white text-sm font-medium"><?php echo number_format($website['rating'], 1); ?>/5.0</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                                        <span class="text-gray-500 text-sm">Total Reviews</span>
                                        <span class="text-gray-900 dark:text-white text-sm font-medium"><?php echo $website['total_ratings']; ?></span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                                        <span class="text-gray-500 text-sm">Category</span>
                                        <span class="text-gray-900 dark:text-white text-sm font-medium"><?php echo htmlspecialchars($website['category_name']); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-gray-500 text-sm">Price</span>
                                        <span class="text-green-600 font-bold text-sm">Free</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Share Card -->
                            <div
                                class="bg-primary/5 rounded-xl border border-primary/20 p-6 flex flex-col items-center text-center gap-4">
                                <h4 class="text-gray-900 dark:text-white font-bold">Love this resource?</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Share it with your community of
                                    learners.</p>
                                <div class="flex gap-4">
                                    <button
                                        class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-primary shadow-sm hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined">share</span>
                                    </button>
                                    <button
                                        class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-primary shadow-sm hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined">link</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <!-- Simple Footer -->
            <footer class="bg-white dark:bg-background-dark border-t border-gray-200 dark:border-gray-800 py-10 mt-12">
                <div class="max-w-[1200px] mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-4 text-gray-500">
                        <div class="size-5">
                            <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M42.4379 44C42.4379 44 36.0744 33.9038 41.1692 24C46.8624 12.9336 42.2078 4 42.2078 4L7.01134 4C7.01134 4 11.6577 12.932 5.96912 23.9969C0.876273 33.9029 7.27094 44 7.27094 44L42.4379 44Z"
                                    fill="currentColor"></path>
                            </svg>
                        </div>
                        <span class="font-bold">ResourceHub</span>
                        <span class="text-sm">© 2023. All rights reserved.</span>
                    </div>
                    <div class="flex gap-8">
                        <a class="text-sm text-gray-500 hover:text-primary" href="#">Privacy Policy</a>
                        <a class="text-sm text-gray-500 hover:text-primary" href="#">Terms of Service</a>
                        <a class="text-sm text-gray-500 hover:text-primary" href="#">Support</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>

</html>
