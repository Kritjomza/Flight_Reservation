<?php 
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('location: login.php');
    exit();
}
include 'server.php';

if (isset($_GET['logout'])) {
    session_destroy();
    session_unset();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agado - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex flex-col md:flex-row min-h-screen">

        <!-- Navbar for small screens -->
        <div class="bg-white p-4 shadow md:hidden flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-700">Admin Dashboard</h2>
            <button onclick="toggleSidebar()">
                <i class="ph ph-list text-2xl text-gray-700"></i>
            </button>
        </div>

        <!-- Sidebar -->
        <?php include 'navbaradmin.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-4 md:p-6">
            <!-- Header -->
            <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow mb-6">
                <div class="flex items-center space-x-3">
                    <i class="ph ph-gauge text-3xl text-indigo-500"></i>
                    <h2 class="text-2xl font-bold text-gray-700">Admin Dashboard</h2>
                </div>
            </div>

            <!-- Data Container -->
            <div id="data-container" class="bg-white p-6 rounded-xl shadow-md transition-all duration-300">
                <div class="flex items-center justify-center text-gray-400 space-x-2">
                    <i class="ph ph-info text-lg"></i>
                    <p>กรุณาเลือกหมวดหมู่จากเมนูด้านซ้าย</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadTable(tableName) {
            fetch('fetch_data.php?table=' + tableName)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('data-container').innerHTML = `<div class="overflow-x-auto">${data}</div>`;
                });
        }

        function loadAirportType(type) {
            fetch(`fetch_data.php?table=airport&type=${type}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('data-container').innerHTML = `<div class="overflow-x-auto">${html}</div>`;
                })
                .catch(err => {
                    console.error("โหลดข้อมูลสนามบินผิดพลาด:", err);
                });
        }

        function toggleDropdown() {
            document.getElementById('ticket-dropdown').classList.toggle('hidden');
        }

        function toggleStatsDropdown() {
            document.getElementById('stats-dropdown').classList.toggle('hidden');
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        }
    </script>

</body>
</html>
