<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: back-office/index.php');
    exit;
}

require_once __DIR__ . '/models/User.php';

$userModel = new User();
$error = '';
$success = '';

// Check for logout message
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $success = 'Vous avez été déconnecté avec succès.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) );
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $user = $userModel->authenticate($username, $password);
      
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            header('Location: back-office/index.php');
            exit;
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }
    }
}
?>
<?php include __DIR__ . '/includes/head.php'; ?>

<body class="bg-background-light dark:bg-background-dark min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
           
            <h2 class="text-2xl font-bold text-[#111418] dark:text-white">Connexion Back-Office</h2>
            <p class="mt-2 text-sm text-[#617589] dark:text-gray-400">
                Connectez-vous pour accéder au panneau d'administration
            </p>
        </div>

        <!-- Login Form -->
        <div class="bg-white dark:bg-[#1a242f] rounded-lg shadow-lg p-8">
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-red-500 mr-2">error</span>
                        <p class="text-sm text-red-700 dark:text-red-400"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-green-500 mr-2">check_circle</span>
                        <p class="text-sm text-green-700 dark:text-green-400"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                        Nom d'utilisateur
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                            <span class="material-symbols-outlined text-xl">person</span>
                        </div>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            required
                            class="block w-full rounded-lg border border-[#f0f2f4] dark:border-gray-600 bg-white dark:bg-gray-700 py-3 pl-10 pr-3 text-sm placeholder-[#617589] focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary transition-all"
                            placeholder="Entrez votre nom d'utilisateur"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                            <span class="material-symbols-outlined text-xl">lock</span>
                        </div>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="block w-full rounded-lg border border-[#f0f2f4] dark:border-gray-600 bg-white dark:bg-gray-700 py-3 pl-10 pr-3 text-sm placeholder-[#617589] focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary transition-all"
                            placeholder="Entrez votre mot de passe" />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full flex justify-center items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                        <span class="material-symbols-outlined">login</span>
                        Se connecter
                    </button>
                </div>
            </form>

            <!-- Demo Credentials -->
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-start">
                    <span class="material-symbols-outlined text-blue-500 mr-2 mt-0.5">info</span>
                    <div class="text-sm">
                        <p class="font-medium text-blue-700 dark:text-blue-400 mb-1">Identifiants de démonstration :</p>
                        <p class="text-blue-600 dark:text-blue-500">Utilisateur : <strong>admin</strong></p>
                        <p class="text-blue-600 dark:text-blue-500">Mot de passe : <strong>admin123</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="index.php" class="text-sm text-[#617589] dark:text-gray-400 hover:text-primary transition-colors">
                ← Retour à l'accueil
            </a>
        </div>
    </div>
</body>
</html>
