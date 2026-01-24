<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

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

$drawerOpen = in_array($action, ['create', 'edit']);
$drawerTitle = $action === 'create' ? 'Create Category' : 'Edit Category';
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Categories</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
    <script src="../public/assets/js/custom.js"></script>
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <aside
            class="w-64 bg-white dark:bg-background-dark border-r border-gray-200 dark:border-gray-800 flex flex-col">
            <div class="p-6 flex flex-col gap-8 h-full">
                <div class="flex items-center gap-3">
                    <div class="bg-primary size-10 rounded-lg flex items-center justify-center text-white">
                        <span class="material-symbols-outlined">auto_stories</span>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-gray-900 dark:text-white text-base font-bold leading-tight">Admin Portal</h1>
                        <p class="text-gray-500 dark:text-gray-400 text-xs font-normal">Back-Office v1.0</p>
                    </div>
                </div>
                <nav class="flex flex-col gap-1 grow">
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        href="websites.php">
                        <span class="material-symbols-outlined text-[24px]">database</span>
                        <p class="text-sm font-medium">Resources</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary"
                        href="categories.php">
                        <span class="material-symbols-outlined text-[24px]">folder</span>
                        <p class="text-sm font-semibold">Categories</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        href="reviews.php">
                        <span class="material-symbols-outlined text-[24px]">chat_bubble</span>
                        <p class="text-sm font-medium">Reviews</p>
                    </a>
                    <div class="my-4 border-t border-gray-100 dark:border-gray-800"></div>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        href="index.php">
                        <span class="material-symbols-outlined text-[24px]">dashboard</span>
                        <p class="text-sm font-medium">Dashboard</p>
                    </a>
                </nav>
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
        <main class="flex-1 flex flex-col overflow-y-auto">
            <header
                class="flex items-center justify-between bg-white dark:bg-background-dark border-b border-gray-200 dark:border-gray-800 px-8 py-4 sticky top-0 z-10">
                <div class="space-y-1">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white">Category Directory</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Manage every category from one panel.</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-xl text-gray-700 dark:text-gray-300">dark_mode</span>
                    </button>
                    <a href="?action=create"
                        class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg font-bold text-sm tracking-wide hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span>New Category</span>
                    </a>
                </div>
            </header>

            <div class="p-8 space-y-6">
                <?php if (!empty($message)): ?>
                    <div class="bg-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-50 dark:bg-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-900/20 border border-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-200 dark:border-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-800 rounded-xl p-4">
                        <p class="text-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-700 dark:text-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-400"><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Categories</div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo count($categories); ?></p>
                        <p class="text-xs text-gray-400 mt-1">Organized categories ready for assignment.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Quick Notice</div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Use the drawer to keep the list visible while adding or editing categories.</p>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Slug</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No categories found. <a href="?action=create" class="text-primary hover:underline">Add one</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white"><?php echo $cat['id']; ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($cat['name']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($cat['slug']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($cat['description']); ?></td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="?action=edit&id=<?php echo $cat['id']; ?>"
                                                        class="p-1.5 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition-colors"
                                                        title="Edit Category">
                                                        <span class="material-symbols-outlined text-[20px]">edit_note</span>
                                                    </a>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                        <input type="hidden" name="delete" value="1">
                                                        <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                                        <button type="submit"
                                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                            title="Delete">
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

                <div class="fixed inset-0 z-40 <?php echo $drawerOpen ? '' : 'pointer-events-none'; ?>" aria-hidden="<?php echo $drawerOpen ? 'false' : 'true'; ?>">
                    <div id="categories-drawer-backdrop"
                        class="absolute inset-0 bg-black/40 transition-opacity <?php echo $drawerOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'; ?>">
                    </div>
                    <section
                        class="absolute inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-gray-800 shadow-2xl transition-transform duration-200 transform <?php echo $drawerOpen ? 'translate-x-0' : 'translate-x-full'; ?> flex flex-col overflow-y-auto"
                        role="dialog" aria-modal="true" aria-label="Categories drawer">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                            <div class="space-y-1">
                                <p class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold">Category desk</p>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($drawerTitle); ?></h3>
                            </div>
                            <a href="categories.php"
                                class="size-10 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-900 dark:text-gray-400 transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </a>
                        </div>
                        <div class="px-6 py-6 space-y-6 flex-1">
                            <form method="POST" class="space-y-6">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="update" value="1">
                                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                <?php else: ?>
                                    <input type="hidden" name="create" value="1">
                                <?php endif; ?>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                                    <input type="text" name="name" value="<?php echo $category ? htmlspecialchars($category['name']) : ''; ?>" required
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Slug *</label>
                                    <input type="text" name="slug" value="<?php echo $category ? htmlspecialchars($category['slug']) : ''; ?>" required
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                    <textarea name="description" rows="5"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary"><?php echo $category ? htmlspecialchars($category['description']) : ''; ?></textarea>
                                </div>

                                <div class="flex items-center gap-4">
                                    <button type="submit"
                                        class="flex-1 bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-600 transition-colors">
                                        <?php echo $action === 'create' ? 'Create' : 'Update'; ?> Category
                                    </button>
                                    <a href="categories.php"
                                        class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
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
    <script>
        (function() {
            const backdrop = document.getElementById('categories-drawer-backdrop');
            const closeDrawer = () => window.location.href = 'categories.php';
            if (backdrop) {
                backdrop.addEventListener('click', closeDrawer);
            }
            document.addEventListener('keydown', (event) => {
                if (<?php echo $drawerOpen ? 'true' : 'false'; ?> && event.key === 'Escape') {
                    closeDrawer();
                }
            });
        })();
    </script>
</body>

</html>

