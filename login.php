<?php include 'server.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - Agado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('./img/bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>
<body class="min-h-screen pt-24">

<div class="flex justify-center items-center min-h-screen px-4">
    <div class="bg-white/90 backdrop-blur p-8 md:p-10 rounded-2xl shadow-xl w-full max-w-md fade-in-up">
        <h2 class="text-3xl font-extrabold text-blue-700 text-center mb-6">Login to Agado</h2>

        <form action="login_db.php" method="POST" class="space-y-5">
            <div class="relative">
                <span class="absolute left-3 top-3.5 text-gray-400">👤</span>
                <input type="text" name="username" id="username" placeholder="Username" required
                    class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300" />
            </div>

            <div class="relative">
                <span class="absolute left-3 top-3.5 text-gray-400">🔒</span>
                <input type="password" name="password" id="password" placeholder="Password" required
                    class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300" />
            </div>

            <button type="submit" name="login_user"
                class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-300 shadow-md hover:shadow-lg">
                Log In
            </button>
        </form>


        <p class="text-center mt-6 text-sm text-gray-600">
            Don't have an account? 
            <a href="register.php" class="text-blue-600 hover:underline font-semibold">Sign Up</a>
        </p>
    </div>
</div>

<style>
    .input {
        @apply w-full p-3 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200;
    }
</style>

</body>
</html>
