<!-- Top Navigation Bar -->
<header
    class="sticky top-0 z-50 w-full border-b border-[#f0f2f4] dark:border-gray-800 bg-white dark:bg-[#1a242f] px-4 lg:px-20">
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
                <?php if (isset($selectedCategoryId) && $selectedCategoryId): ?>
                    <input type="hidden" name="category" value="<?php echo $selectedCategoryId; ?>">
                    <input type="hidden" name="category_slug" value="<?php echo htmlspecialchars($selectedCategorySlug ?? ''); ?>">
                <?php endif; ?>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                    <span class="material-symbols-outlined text-xl">search</span>
                </div>
                <input
                    name="search"
                    value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>"
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