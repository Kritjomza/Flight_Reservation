<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('location: login.php');
    exit();
}

include 'server.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    die("Invalid passenger ID.");
}

// ดึงข้อมูลเดิม
$query = "SELECT p.*, u.username, u.email FROM Passenger p JOIN User u ON p.user_id = u.user_id WHERE p.passenger_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$passenger = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $citizen_id = $_POST['citizen_id'];
    $passport_id = $_POST['passport_id'];
    $gender = $_POST['gender'];

    $updateQuery = "UPDATE Passenger SET first_name=?, last_name=?, citizen_id=?, passport_id=?, gender=? WHERE passenger_id=?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssssi", $first_name, $last_name, $citizen_id, $passport_id, $gender, $id);
    $updateStmt->execute();

    header("Location: manage_passenger.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Passenger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Edit Passenger</h1>
        <form method="POST">
            <label class="block mb-2">First Name</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($passenger['first_name']) ?>" required class="w-full p-2 border rounded mb-4">

            <label class="block mb-2">Last Name</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($passenger['last_name']) ?>" required class="w-full p-2 border rounded mb-4">

            <label class="block mb-2">Citizen ID</label>
            <input type="text" name="citizen_id" value="<?= htmlspecialchars($passenger['citizen_id']) ?>" required class="w-full p-2 border rounded mb-4">

            <label class="block mb-2">Passport ID</label>
            <input type="text" name="passport_id" value="<?= htmlspecialchars($passenger['passport_id']) ?>" required class="w-full p-2 border rounded mb-4">

            <label class="block mb-2">Gender</label>
            <select name="gender" class="w-full p-2 border rounded mb-4">
                <option value="M" <?= $passenger['gender'] == 'M' ? 'selected' : '' ?>>Male</option>
                <option value="F" <?= $passenger['gender'] == 'F' ? 'selected' : '' ?>>Female</option>
            </select>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
            <a href="manage_passenger.php" class="ml-4 text-gray-600 hover:underline">Cancel</a>
        </form>
    </div>
</body>
</html>
