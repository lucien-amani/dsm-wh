<?php
require_once '../config/database.php';
require_once 'includes/auth.php';

$pdo = getDBConnection();
$admin_id = $_SESSION['admin_id'];
$error = '';
$success = '';

// Récupérer les infos actuelles
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $admin_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $username = $_POST['username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    // Upload image
    $image_name = $user['avatar'] ?? null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $upload_dir = '../uploads/avatars/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $new_name = 'avatar_' . $admin_id . '_' . time() . '.' . $extension;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_dir . $new_name)) {
            $image_name = $new_name;
        }
    }

    if ($full_name && $email && $username) {
        try {
            $sql = "UPDATE users SET full_name = :full_name, email = :email, phone = :phone, username = :username, avatar = :avatar";
            $params = [
                ':full_name' => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':username' => $username,
                ':avatar' => $image_name,
                ':id' => $admin_id
            ];

            if (!empty($new_password)) {
                $sql .= ", password = :password";
                $params[':password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // Mettre à jour la session
            $_SESSION['admin_name'] = $full_name;
            
            header('Location: ' . SITE_URL . '/admin/profil?msg=updated');
            exit;
        } catch (PDOException $e) {
            $error = "Erreur SQL : " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir les informations de base.";
    }
}

$current_page = 'profile';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - DSM ADMIN</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/dist/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 flex transition-colors duration-300 min-h-screen">
    
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/toast.php'; ?>

    <main class="flex-1 lg:ml-72 flex flex-col min-w-0">
        <!-- Header -->
        <header class="h-24 flex items-center justify-between px-8 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800/50">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Mon <span class="text-emerald-600">Profil</span></h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-1">Paramètres du compte</p>
            </div>
        </header>

        <div class="p-8 max-w-4xl mx-auto w-full animate-fade-in">
            <?php if($error): ?>
                <div class="mb-8 p-6 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-3xl text-sm font-bold animate-shake">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-8">
                <!-- Profile Identity Card -->
                <div class="bg-white dark:bg-slate-900 p-10 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 text-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <!-- Avatar Upload -->
                    <div class="relative w-32 h-32 mx-auto mb-6">
                        <div class="w-full h-full rounded-[2.5rem] bg-emerald-100 dark:bg-emerald-900/30 overflow-hidden border-4 border-white dark:border-slate-800 shadow-xl">
                            <?php if($user['avatar']): ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/avatars/<?php echo $user['avatar']; ?>" id="avatar-preview" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div id="avatar-placeholder" class="w-full h-full flex items-center justify-center text-4xl font-black text-emerald-600">
                                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                </div>
                                <img id="avatar-preview" class="w-full h-full object-cover hidden">
                            <?php endif; ?>
                        </div>
                        <label class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg cursor-pointer hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <input type="file" name="avatar" class="sr-only" onchange="previewAvatar(this)">
                        </label>
                    </div>
                    
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mt-1"><?php echo $user['role']; ?></p>
                </div>

                <!-- Form Fields -->
                <div class="bg-white dark:bg-slate-900 p-10 rounded-[3rem] shadow-sm border border-slate-200 dark:border-slate-800 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Nom Complet</label>
                            <input type="text" name="full_name" required value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                                   class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Identifiant (Username)</label>
                            <input type="text" name="username" required value="<?php echo htmlspecialchars($user['username']); ?>" 
                                   class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">E-mail Professionnel</label>
                            <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Téléphone</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" 
                                   class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/50 space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Changer le mot de passe (Laisser vide si inchangé)</label>
                        <div class="relative group">
                            <input type="password" id="new_password" name="new_password" placeholder="Nouveau mot de passe..."
                                   class="block w-full px-8 py-4 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl focus:ring-4 focus:ring-emerald-500/10 transition-all dark:text-white font-bold text-sm">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-4 flex items-center text-slate-400 hover:text-emerald-600 transition-colors focus:outline-none">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-5 rounded-[2rem] font-black uppercase tracking-widest text-xs shadow-xl shadow-emerald-600/20 hover:-translate-y-1 transition-all">
                            Mettre à jour mon profil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewAvatar(input) {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-placeholder');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                if(placeholder) placeholder.classList.add('hidden');
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('new_password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
