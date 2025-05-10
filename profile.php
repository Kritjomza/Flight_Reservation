<?php
session_start();
include 'server.php';
include 'navbar.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// ดึงข้อมูลจาก User และ Passenger
$stmt = $conn->prepare("
    SELECT U.username, U.email, P.first_name, P.last_name, P.gender 
    FROM User U 
    LEFT JOIN Passenger P ON U.user_id = P.user_id 
    WHERE U.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// หากส่งฟอร์มมา
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    // ตรวจสอบรหัสผ่านเดิม
    $stmt = $conn->prepare("SELECT password FROM User WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if (!password_verify($current_password, $row['password'])) {
        $error = "รหัสผ่านปัจจุบันไม่ถูกต้อง";
    } else {
        // อัปเดต User
        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE User SET email = ?, password = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $email, $hashed, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE User SET email = ? WHERE user_id = ?");
            $stmt->bind_param("si", $email, $user_id);
        }
        $stmt->execute();

        // อัปเดต Passenger
        $stmt = $conn->prepare("UPDATE Passenger SET first_name = ?, last_name = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $first_name, $last_name, $user_id);
        $stmt->execute();

        $success = "อัปเดตข้อมูลสำเร็จ";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>จัดการโปรไฟล์</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-lg w-full">
        <h2 class="text-2xl font-bold text-blue-600 mb-6 text-center">👤 จัดการโปรไฟล์</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium">ชื่อ</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>"
                    class="w-full px-4 py-2 border rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium">นามสกุล</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>"
                    class="w-full px-4 py-2 border rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium">อีเมล</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                    class="w-full px-4 py-2 border rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium">รหัสผ่านปัจจุบัน <span class="text-red-500">*</span></label>
                <input type="password" name="current_password" required
                    class="w-full px-4 py-2 border rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium">รหัสผ่านใหม่ (ไม่กรอกหากไม่เปลี่ยน)</label>
                <input type="password" name="new_password"
                    class="w-full px-4 py-2 border rounded-md">
            </div>

            <div class="text-center">
                <button type="submit"
                    class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-md transition">
                    บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>
</body>
</html>
