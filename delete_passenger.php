<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('location: login.php');
    exit();
}

include 'server.php';

$passenger_id = $_GET['id'] ?? '';
if (!$passenger_id) {
    die("Invalid passenger ID.");
}

// ดึงข้อมูลผู้โดยสาร
$query = "SELECT p.passenger_id, p.first_name, p.last_name, u.user_id, u.username 
          FROM Passenger p 
          JOIN User u ON p.user_id = u.user_id 
          WHERE p.passenger_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $passenger_id);
$stmt->execute();
$result = $stmt->get_result();
$passenger = $result->fetch_assoc();

if (!$passenger) {
    die("Passenger not found.");
}

// หากกดยืนยันการลบ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $user_id = $passenger['user_id'];
    $deleteQuery = "DELETE FROM User WHERE user_id = ?";
    $delStmt = $conn->prepare($deleteQuery);
    $delStmt->bind_param("i", $user_id);
    $delStmt->execute();

    header("Location: manage_passenger.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Delete Passenger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white shadow p-6 rounded">
        <h1 class="text-xl font-semibold text-red-600 mb-4">Confirm Delete</h1>
        <p class="mb-4">
            Are you sure you want to delete the following passenger?
        </p>
        <ul class="mb-6">
            <li><strong>ID:</strong> <?= htmlspecialchars($passenger['passenger_id']) ?></li>
            <li><strong>Name:</strong> <?= htmlspecialchars($passenger['first_name'] . ' ' . $passenger['last_name']) ?></li>
            <li><strong>Username:</strong> <?= htmlspecialchars($passenger['username']) ?></li>
        </ul>
        <form method="POST">
            <button type="submit" name="confirm_delete" class="bg-red-500 text-white px-4 py-2 rounded">Yes, Delete</button>
            <a href="manage_passenger.php" class="ml-4 text-gray-600 hover:underline">Cancel</a>
        </form>
    </div>
</body>
</html>
