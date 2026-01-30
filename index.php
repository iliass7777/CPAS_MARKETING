<?php
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Website.php';

$categoryModel = new Category();
$websiteModel = new Website();

// Get all categories
$categories = $categoryModel->getAll();

// Get filters
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$selectedCategorySlug = isset($_GET['category_slug']) ? $_GET['category_slug'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$minRating = isset($_GET['min_rating']) ? (int)$_GET['min_rating'] : 0;

$filters = [
    'search' => $searchTerm,
    'category_id' => $selectedCategoryId,
    'sort' => $sort,
    'min_rating' => $minRating
];

$itemsPerPage = 6;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get websites using the new versatile method
$websites = $websiteModel->getWebsites($filters, $itemsPerPage, $offset);
$totalWebsites = $websiteModel->getWebsitesCount($filters);

$totalPages = ceil($totalWebsites / $itemsPerPage);

// Icon mapping for categories
$categoryIcons = [
    'coding' => 'code',
    'design' => 'palette',
    'general culture' => 'public',
    'productivity' => 'bolt',
    'marketing' => 'trending_up',
    'education' => 'school',
    'default' => 'dashboard'
];

function getCategoryIcon($categoryName, $categoryIcons) {
    $nameLower = strtolower($categoryName);
    foreach ($categoryIcons as $key => $icon) {
        if (strpos($nameLower, $key) !== false) {
            return $icon;
        }
    }
    return $categoryIcons['default'];
}

function getCategoryColor($categoryName) {
    $nameLower = strtolower($categoryName);
    if (strpos($nameLower, 'coding') !== false || strpos($nameLower, 'code') !== false) {
        return 'text-primary';
    } elseif (strpos($nameLower, 'design') !== false) {
        return 'text-orange-500';
    } elseif (strpos($nameLower, 'general') !== false || strpos($nameLower, 'culture') !== false) {
        return 'text-green-500';
    } elseif (strpos($nameLower, 'productivity') !== false) {
        return 'text-purple-500';
    } elseif (strpos($nameLower, 'marketing') !== false) {
        return 'text-red-500';
    }
    return 'text-primary';
}

function formatRating($rating) {
    return number_format((float)$rating, 1);
}

function buildPaginationUrl($page, $filters) {
    $params = [];
    if (!empty($filters['search'])) {
        $params[] = 'search=' . urlencode($filters['search']);
    }
    if (!empty($filters['category_id'])) {
        $params[] = 'category=' . $filters['category_id'];
        // Note: category_slug might be missing here if not passed in filters but strictly only needed for URL prettiness or handled
        if (isset($_GET['category_slug'])) {
            $params[] = 'category_slug=' . urlencode($_GET['category_slug']);
        }
    }
    if (!empty($filters['sort']) && $filters['sort'] !== 'popular') {
        $params[] = 'sort=' . $filters['sort'];
    }
    if (!empty($filters['min_rating'])) {
        $params[] = 'min_rating=' . $filters['min_rating'];
    }
    
    if ($page > 1) {
        $params[] = 'page=' . $page;
    }
    return 'index.php' . (!empty($params) ? '?' . implode('&', $params) : '');
}

function buildFilterUrl($key, $value) {
    $params = $_GET;
    // Update or add the filter
    if ($value === null) {
        unset($params[$key]);
    } else {
        $params[$key] = $value;
    }
    // Reset page when filtering
    unset($params['page']);
    
    return 'index.php' . (!empty($params) ? '?' . http_build_query($params) : '');
}
?>
<?php include __DIR__ . '/includes/head.php'; ?>

<body class="bg-background-light dark:bg-background-dark text-[#111418] dark:text-white transition-colors duration-200">
<?php include __DIR__ . '/includes/header.php'; ?>
    <main class="max-w-[1440px] mx-auto flex flex-col px-4 lg:px-20 py-8">
        <!-- Page Heading & Category Chips -->
        <div class="flex flex-col gap-6 mb-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <h2 class="text-3xl font-black leading-tight tracking-tight text-[#111418] dark:text-white">Discover
                        Top Resources</h2>
                    <p class="text-[#617589] dark:text-gray-400">Curated websites and tools for professionals and
                        enthusiasts.</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-[#617589]">
                    <span>Sorted by:</span>
                    <button class="flex items-center gap-1 font-bold text-[#111418] dark:text-white">
                        Most Popular <span class="material-symbols-outlined text-sm">expand_more</span>
                    </button>
                </div>
            </div>
            <!-- Categories -->
            <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar">
                <a href="index.php"
                    class="flex items-center gap-2 h-9 px-4 rounded-full <?php echo !$selectedCategoryId ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border border-[#f0f2f4] dark:border-gray-700 hover:border-primary transition-colors'; ?> text-sm font-medium whitespace-nowrap">
                    <span class="material-symbols-outlined text-lg">dashboard</span> All Categories
                </a>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <?php 
                        $isActive = $selectedCategoryId == $category['id'];
                        $icon = getCategoryIcon($category['name'], $categoryIcons);
                        $color = getCategoryColor($category['name']);
                        ?>
                        <a href="index.php?category=<?php echo $category['id']; ?>&category_slug=<?php echo urlencode($category['slug']); ?>"
                            class="flex items-center gap-2 h-9 px-4 rounded-full <?php echo $isActive ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border border-[#f0f2f4] dark:border-gray-700 hover:border-primary transition-colors'; ?> text-sm font-medium whitespace-nowrap">
                            <span class="material-symbols-outlined text-lg <?php echo $isActive ? '' : $color; ?>"><?php echo $icon; ?></span> 
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Side Navigation (Filters) -->
            <aside class="w-full md:w-64 shrink-0 flex flex-col gap-8">
                <div
                    class="bg-white dark:bg-[#1a242f] p-6 rounded-xl border border-[#f0f2f4] dark:border-gray-800 shadow-sm sticky top-24">
                    <div class="flex flex-col gap-6">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#617589] mb-4">Filters</h3>
                            <div class="flex flex-col gap-1">
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?php echo $sort === 'popular' && empty($minRating) ? 'bg-primary/10 text-primary font-bold' : 'hover:bg-background-light dark:hover:bg-gray-800 text-[#617589] dark:text-gray-300 font-medium'; ?> transition-colors"
                                    href="index.php">
                                    <span class="material-symbols-outlined">auto_awesome</span> All Resources
                                </a>
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?php echo $sort === 'top_rated' ? 'bg-primary/10 text-primary font-bold' : 'hover:bg-background-light dark:hover:bg-gray-800 text-[#617589] dark:text-gray-300 font-medium'; ?> transition-colors"
                                    href="<?php echo buildFilterUrl('sort', 'top_rated'); ?>">
                                    <span class="material-symbols-outlined">stars</span> Top Rated
                                </a>
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?php echo $sort === 'popular' && !empty($minRating) ? '' : ($sort === 'popular' ? 'bg-primary/10 text-primary font-bold' : 'hover:bg-background-light dark:hover:bg-gray-800 text-[#617589] dark:text-gray-300 font-medium'); ?> transition-colors"
                                    href="<?php echo buildFilterUrl('sort', 'popular'); ?>">
                                    <span class="material-symbols-outlined">trending_up</span> Most Popular
                                </a>
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?php echo $sort === 'newest' ? 'bg-primary/10 text-primary font-bold' : 'hover:bg-background-light dark:hover:bg-gray-800 text-[#617589] dark:text-gray-300 font-medium'; ?> transition-colors"
                                    href="<?php echo buildFilterUrl('sort', 'newest'); ?>">
                                    <span class="material-symbols-outlined">new_releases</span> Newest
                                </a>
                            </div>
                        </div>
                        <div class="h-px bg-[#f0f2f4] dark:bg-gray-800"></div>
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#617589] mb-4">Rating</h3>
                            <div class="flex flex-col gap-2">
                                <a href="<?php echo buildFilterUrl('min_rating', 4); ?>" class="flex items-center justify-between group <?php echo $minRating == 4 ? 'bg-primary/5 rounded-lg p-1' : ''; ?>">
                                    <div class="flex text-yellow-500">
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined">star</span>
                                    </div>
                                    <span class="text-xs text-[#617589] font-medium group-hover:text-primary transition-colors">&amp; Up</span>
                                </a>
                                <a href="<?php echo buildFilterUrl('min_rating', 3); ?>" class="flex items-center justify-between group <?php echo $minRating == 3 ? 'bg-primary/5 rounded-lg p-1' : ''; ?>">
                                    <div class="flex text-yellow-500">
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined fill-1">star</span>
                                        <span class="material-symbols-outlined">star</span>
                                        <span class="material-symbols-outlined">star</span>
                                    </div>
                                    <span class="text-xs text-[#617589] font-medium group-hover:text-primary transition-colors">& Up</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <!-- Main Resource Grid -->
            <div class="flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($websites)): ?>
                        <?php foreach ($websites as $website): ?>
                            <?php 
                            $isTopLeader = $website['rating'] >= 4.5 && $website['total_ratings'] >= 100;
                            ?>
                    <div
                        class="bg-white dark:bg-[#1a242f] rounded-xl border border-[#f0f2f4] dark:border-gray-800 overflow-hidden shadow-sm hover:shadow-lg transition-all group">
                        <div class="relative h-48 bg-[#f8fafc] dark:bg-gray-800">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                                        data-alt="<?php echo htmlspecialchars($website['name']); ?> website preview"
                                        style='background-image: url("https://via.placeholder.com/400x200/137fec/ffffff?text=<?php echo urlencode($website['name']); ?>");'>
                            </div>
                                    <?php if ($isTopLeader): ?>
                            <div class="absolute top-3 right-3">
                                <span
                                    class="bg-primary text-white text-[10px] font-black uppercase px-2 py-1 rounded-full flex items-center gap-1 shadow-lg">
                                    <span class="material-symbols-outlined text-xs">verified</span> Top Leader
                                </span>
                            </div>
                                    <?php endif; ?>
                        </div>
                        <div class="p-5 flex flex-col gap-4">
                            <div>
                                        <h3 class="text-lg font-bold text-[#111418] dark:text-white mb-1">
                                            <?php echo htmlspecialchars($website['name']); ?>
                                        </h3>
                                        <p class="text-sm text-[#617589] dark:text-gray-400 line-clamp-2">
                                            <?php echo htmlspecialchars($website['description'] ?: 'No description available.'); ?>
                                        </p>
                            </div>
                                    <div class="flex items-center">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-yellow-500 fill-1 text-base">star</span>
                                            <span class="text-sm font-bold text-[#111418] dark:text-white">
                                                <?php echo formatRating($website['rating']); ?>
                                            </span>
                                            <span class="text-xs text-[#617589]">
                                                (<?php echo $website['total_ratings']; ?> reviews)
                                </span>
                            </div>
                        </div>
                                    <a href="website.php?id=<?php echo $website['id']; ?>"
                                        class="w-full h-10 rounded-lg border border-primary text-primary font-bold text-sm hover:bg-primary hover:text-white transition-colors flex items-center justify-center">
                                View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-12">
                            <?php if (!empty($searchTerm)): ?>
                                <p class="text-[#617589] dark:text-gray-400">No websites found for "<?php echo htmlspecialchars($searchTerm); ?>". Try a different search term.</p>
                            <?php else: ?>
                                <p class="text-[#617589] dark:text-gray-400">No websites found. Check back later!</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="mt-12 flex justify-center">
                    <nav class="flex items-center gap-2">
                            <!-- Bouton Précédent -->
                            <?php if ($currentPage > 1): ?>
                                <a href="<?php echo buildPaginationUrl($currentPage - 1, $searchTerm, $selectedCategoryId, $selectedCategorySlug); ?>"
                            class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800 transition-colors">
                            <span class="material-symbols-outlined">chevron_left</span>
                                </a>
                            <?php else: ?>
                                <span class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 opacity-50 cursor-not-allowed">
                                    <span class="material-symbols-outlined">chevron_left</span>
                                </span>
                            <?php endif; ?>
                            
                            <!-- Numéros de pages -->
                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
                            // Afficher la première page si nécessaire
                            if ($startPage > 1): ?>
                                <a href="<?php echo buildPaginationUrl(1, $searchTerm, $selectedCategoryId, $selectedCategorySlug); ?>"
                                    class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800 transition-colors">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span class="mx-2 text-[#617589]">...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <!-- Pages autour de la page actuelle -->
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i == $currentPage): ?>
                                    <span class="size-10 flex items-center justify-center rounded-lg bg-primary text-white font-bold"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="<?php echo buildPaginationUrl($i, $searchTerm, $selectedCategoryId, $selectedCategorySlug); ?>"
                                        class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800 transition-colors"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <!-- Afficher la dernière page si nécessaire -->
                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                        <span class="mx-2 text-[#617589]">...</span>
                                <?php endif; ?>
                                <a href="<?php echo buildPaginationUrl($totalPages, $searchTerm, $selectedCategoryId, $selectedCategorySlug); ?>"
                                    class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800 transition-colors"><?php echo $totalPages; ?></a>
                            <?php endif; ?>
                            
                            <!-- Bouton Suivant -->
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="<?php echo buildPaginationUrl($currentPage + 1, $searchTerm, $selectedCategoryId, $selectedCategorySlug); ?>"
                            class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 hover:bg-white dark:hover:bg-gray-800 transition-colors">
                            <span class="material-symbols-outlined">chevron_right</span>
                                </a>
                            <?php else: ?>
                                <span class="size-10 flex items-center justify-center rounded-lg border border-[#f0f2f4] dark:border-gray-800 opacity-50 cursor-not-allowed">
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </span>
                            <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php include __DIR__ . '/includes/footer.php'; ?>
