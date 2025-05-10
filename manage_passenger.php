<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('location: login.php');
    exit();
}
include 'server.php';
// include 'navbaradmin.php';

$search = $_GET['search'] ?? '';

$query = "SELECT p.passenger_id, u.username, u.email, p.first_name, p.last_name, p.citizen_id, p.passport_id, p.gender 
          FROM Passenger p
          JOIN User u ON p.user_id = u.user_id
          WHERE CONCAT(u.username, ' ', p.first_name, ' ', p.last_name, ' ', p.citizen_id, ' ', p.passport_id) LIKE '%$search%'";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Passengers</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-6">
    <!-- ปุ่มย้อนกลับ -->
    <div class="mb-4">
        <a href="admin_dashboard.php" class="text-blue-600 hover:underline">&larr; กลับไปหน้า Dashboard</a>
    </div>

    <!-- หัวข้อ -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
            🧍 จัดการผู้โดยสาร
        </h1>

        <!-- Search -->
        <form method="GET" class="flex items-center space-x-2 mb-4">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="ค้นหาชื่อ, username, ID..." class="w-full px-4 py-2 border rounded-md">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">ค้นหา</button>
        </form>

        <!-- ตาราง -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded-lg">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">Passenger ID</th>
                        <th class="px-4 py-2 border">Username</th>
                        <th class="px-4 py-2 border">Email</th>
                        <th class="px-4 py-2 border">First Name</th>
                        <th class="px-4 py-2 border">Last Name</th>
                        <th class="px-4 py-2 border">Citizen ID</th>
                        <th class="px-4 py-2 border">Passport ID</th>
                        <th class="px-4 py-2 border">Gender</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border"><?= $row['passenger_id'] ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['username']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['first_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['last_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['citizen_id']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['passport_id']) ?></td>
                            <td class="px-4 py-2 border"><?= $row['gender'] ?></td>
                            <td class="px-4 py-2 border text-center">
                                <a href="edit_passenger.php?id=<?= urlencode($row['passenger_id']) ?>" class="text-blue-600 hover:underline mr-2">Edit</a>
                                <a href="delete_passenger.php?id=<?= urlencode($row['passenger_id']) ?>" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้โดยสารนี้?')" class="text-red-600 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="9" class="text-center text-gray-500 py-4">ไม่พบข้อมูลผู้โดยสาร</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
