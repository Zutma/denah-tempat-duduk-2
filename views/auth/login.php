<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Wisuda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans text-gray-900 antialiased">

    <!-- Container Utama dengan Background Gambar Denah -->
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-center bg-no-repeat relative"
         style="background-image: url('/images/bg-denah.png');">
        
        <!-- Overlay tipis biar card login & logo lebih menonjol -->
        <div class="absolute inset-0 bg-slate-900/10 backdrop-blur-[1px]"></div>

        <div class="relative z-10 w-full flex flex-col items-center px-4">
            <!-- Logo -->
            <div class="mb-6 text-center">
                <img src="/images/logo.png" alt="Logo" class="w-[140px] h-auto mx-auto drop-shadow-md">
            </div>

            <!-- Form Card -->
            <div class="w-full sm:max-w-md px-6 py-8 bg-white/95 backdrop-blur-md shadow-2xl overflow-hidden sm:rounded-xl border border-white/20">
                <h2 class="text-xl font-bold text-gray-800 mb-1">Masuk ke Admin Portal</h2>
                <p class="text-sm text-gray-500 mb-6">Sistem Informasi Denah Tempat Duduk Wisuda</p>

                <?php if (!empty($errors)): ?>
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            required autofocus
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password"
                            required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                    </div>

                    <div class="flex items-center justify-end pt-1">
                        <button type="submit"
                            class="px-6 py-2.5 bg-[#0f172a] hover:bg-slate-800 text-white font-semibold text-sm rounded-lg shadow-sm transition-colors">
                            Log In
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</body>
</html>