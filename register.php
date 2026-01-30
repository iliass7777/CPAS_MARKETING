<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/models/User.php';

$userModel = new User();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        // Create user with default 'user' role
        $result = $userModel->create($username, $email, $password, $fullName, 'user');
        
        if (isset($result['success']) && $result['success']) {
            $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            // Optional: Auto login or redirect to login
            // For now, redirect to login with success message
            header('Location: login.php?registered=1');
            exit;
        } else {
            $error = $result['message'] ?? 'Une erreur est survenue lors de l\'inscription.';
        }
    }
}
?>
<?php include __DIR__ . '/includes/head.php'; ?>

<body class="bg-background-light dark:bg-background-dark min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full space-y-8 my-8">
        <!-- Header -->
        <div class="text-center">
            <div class="flex items-center justify-center gap-3 mb-6">
                <div class="text-primary">
                    <span class="material-symbols-outlined text-4xl">hub</span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-[#111418] dark:text-white">ResourceHub</h1>
            </div>
            <h2 class="text-2xl font-bold text-[#111418] dark:text-white">Créer un compte</h2>
            <p class="mt-2 text-sm text-[#617589] dark:text-gray-400">
                Rejoignez notre communauté pour évaluer les meilleures ressources
            </p>
        </div>

        <!-- Register Form -->
        <div class="bg-white dark:bg-[#1a242f] rounded-lg shadow-lg p-8">
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-red-500 mr-2">error</span>
                        <p class="text-sm text-red-700 dark:text-red-400"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" class="space-y-6">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                        Nom complet *
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                            <span class="material-symbols-outlined text-xl">badge</span>
                        </div>
                        <input
                            id="full_name"
                            name="full_name"
                            type="text"
                            required
                            class="block w-full rounded-lg border border-[#f0f2f4] dark:border-gray-600 bg-white dark:bg-gray-700 py-3 pl-10 pr-3 text-sm placeholder-[#617589] focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary transition-all"
                            placeholder="Votre nom"
                            value="<?php echo htmlspecialchars($fullName ?? ''); ?>" />
                    </div>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                        Nom d'utilisateur *
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
                            placeholder="Choisissez un nom d'utilisateur"
                            value="<?php echo htmlspecialchars($username ?? ''); ?>" />
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                        Email *
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                            <span class="material-symbols-outlined text-xl">mail</span>
                        </div>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            class="block w-full rounded-lg border border-[#f0f2f4] dark:border-gray-600 bg-white dark:bg-gray-700 py-3 pl-10 pr-3 text-sm placeholder-[#617589] focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary transition-all"
                            placeholder="Votre adresse email"
                            value="<?php echo htmlspecialchars($email ?? ''); ?>" />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                        Mot de passe *
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
                            minlength="6"
                            class="block w-full rounded-lg border border-[#f0f2f4] dark:border-gray-600 bg-white dark:bg-gray-700 py-3 pl-10 pr-3 text-sm placeholder-[#617589] focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary transition-all"
                            placeholder="Minimum 6 caractères" />
                    </div>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-[#111418] dark:text-white mb-2">
                        Confirmer le mot de passe *
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#617589]">
                            <span class="material-symbols-outlined text-xl">lock_reset</span>
                        </div>
                        <input
                            id="confirm_password"
                            name="confirm_password"
                            type="password"
                            required
                            minlength="6"
                            class="block w-full rounded-lg border border-[#f0f2f4] dark:border-gray-600 bg-white dark:bg-gray-700 py-3 pl-10 pr-3 text-sm placeholder-[#617589] focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary transition-all"
                            placeholder="Répétez le mot de passe" />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full flex justify-center items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                        <span class="material-symbols-outlined">person_add</span>
                        S'inscrire
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to Login/Home -->
        <div class="text-center space-y-2">
            <p class="text-sm text-[#617589] dark:text-gray-400">
                Déjà un compte ? 
                <a href="login.php" class="text-primary font-bold hover:underline">Se connecter</a>
            </p>
            <div>
                <a href="index.php" class="text-sm text-[#617589] dark:text-gray-400 hover:text-primary transition-colors">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</body>
</html>
