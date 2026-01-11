    <!-- Footer -->
    <footer class="mt-20 border-t border-[#f0f2f4] dark:border-gray-800 bg-white dark:bg-[#1a242f] py-12 px-4 lg:px-20">
        <div class="max-w-[1440px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1 flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="text-primary">
                        <span class="material-symbols-outlined text-3xl">hub</span>
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-[#111418] dark:text-white">ResourceHub</h2>
                </div>
                <p class="text-sm text-[#617589] dark:text-gray-400">Discovering and ranking the web's best tools since
                    2023.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">Categories</h4>
                <ul class="flex flex-col gap-2 text-sm text-[#617589] dark:text-gray-400">
                    <?php if (!empty($categories)): ?>
                        <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                            <li>
                                <a class="hover:text-primary" href="index.php?category=<?php echo $cat['id']; ?>&category_slug=<?php echo urlencode($cat['slug']); ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a class="hover:text-primary" href="index.php">Development</a></li>
                        <li><a class="hover:text-primary" href="index.php">Graphic Design</a></li>
                        <li><a class="hover:text-primary" href="index.php">Marketing Tools</a></li>
                        <li><a class="hover:text-primary" href="index.php">Education</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Support</h4>
                <ul class="flex flex-col gap-2 text-sm text-[#617589] dark:text-gray-400">
                    <li><a class="hover:text-primary" href="#">Help Center</a></li>
                    <li><a class="hover:text-primary" href="#">Submit a Resource</a></li>
                    <li><a class="hover:text-primary" href="#">API Documentation</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Newsletter</h4>
                <p class="text-sm text-[#617589] dark:text-gray-400 mb-4">Get the weekly top leaders list in your inbox.
                </p>
                <div class="flex gap-2">
                    <input class="flex-1 rounded-lg border-[#f0f2f4] dark:border-gray-800 dark:bg-gray-800 text-sm"
                        placeholder="Email" type="email" />
                    <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold">Join</button>
                </div>
            </div>
        </div>
        <div
            class="max-w-[1440px] mx-auto mt-12 pt-8 border-t border-[#f0f2f4] dark:border-gray-800 flex justify-between items-center text-xs text-[#617589]">
            <p>© 2024 ResourceHub Inc. All rights reserved.</p>
            <div class="flex gap-4">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>
