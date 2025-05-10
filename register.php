<?php include 'server.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Agado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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
            animation: fadeInUp 1s ease-out;
        }

        body {
            background-image: url('./img/bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="bg-cover bg-center min-h-screen pt-24">

<div class="flex justify-center items-center min-h-screen px-4">
    <div class="bg-white/90 backdrop-blur p-8 md:p-10 rounded-2xl shadow-xl w-full max-w-xl fade-in-up">
        <h2 class="text-3xl font-extrabold text-blue-700 text-center mb-6 tracking-wide">Create Your Account</h2>
        
        <form action="register_db.php" method="POST" class="space-y-5">
            <!-- Username + Email -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative w-full">
                    <span class="absolute left-3 top-3.5 text-gray-400">👤</span>
                    <input type="text" name="username" placeholder="Username" required
                        class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
                <div class="relative w-full">
                    <span class="absolute left-3 top-3.5 text-gray-400">📧</span>
                    <input type="email" name="email" placeholder="Email" required
                        class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
            </div>

            <!-- Passwords -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative w-full">
                    <span class="absolute left-3 top-3.5 text-gray-400">🔒</span>
                    <input type="password" name="password1" placeholder="Password" required
                        class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
                <div class="relative w-full">
                    <span class="absolute left-3 top-3.5 text-gray-400">🔒</span>
                    <input type="password" name="password2" placeholder="Confirm Password" required
                        class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
            </div>

            <!-- Name -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative w-full">
                    <span class="absolute left-3 top-3.5 text-gray-400">🧑</span>
                    <input type="text" name="first_name" placeholder="First Name" required
                        class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
                <div class="relative w-full">
                    <span class="absolute left-3 top-3.5 text-gray-400">👨‍🦱</span>
                    <input type="text" name="last_name" placeholder="Last Name" required
                        class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
            </div>

            <!-- IDs -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative w-full">
                    <span class="absolute left-3 top-3.5 text-gray-400">🆔</span>
                    <input type="text" name="citizen_id" placeholder="Citizen ID" required
                        class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
                <div class="relative w-full">
                    <span class="absolute left-3 top-3.5 text-gray-400">🛂</span>
                    <input type="text" name="passport_id" placeholder="Passport ID (optional)"
                        class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
            </div>

            <!-- Gender -->
            <div class="relative">
                <span class="absolute left-3 top-3.5 text-gray-400">⚧️</span>
                <select name="gender" required
                    class="pl-10 pr-4 py-3 w-full rounded-lg shadow-md bg-white/90 border border-gray-300 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Select Gender</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-300 shadow-md hover:shadow-lg"
                name="reg_user">
                Sign Up
            </button>
        </form>


        <p class="text-center mt-6 text-sm text-gray-600">
            Already have an account?
            <a href="login.php" class="text-blue-600 hover:underline font-semibold">Log In</a>
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
