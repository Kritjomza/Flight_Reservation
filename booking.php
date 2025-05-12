<?php
include('server.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อนทำการจอง");
}

$user_id = $_SESSION['user_id'];
$flight_id = $_POST['flight_id'] ?? null;
$seat_no = $_POST['seat_no'] ?? 'AUTO';

$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$phone = $_POST['phone_number'] ?? '';
$email = $_POST['email'] ?? '';

if (!$flight_id) {
    die("ไม่พบรหัสเที่ยวบิน");
}

// ✅ ดึง passenger_id จากตาราง Passenger
$stmt = $conn->prepare("SELECT passenger_id FROM Passenger WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$passenger_result = $stmt->get_result();
$passenger = $passenger_result->fetch_assoc();
$passenger_id = $passenger['passenger_id'] ?? null;

if (!$passenger_id) {
    die("ไม่พบข้อมูลผู้โดยสารในระบบ");
}

// ✅ STEP 1: สร้าง Booking
$stmt = $conn->prepare("
    INSERT INTO Booking (user_id, flight_id, status, first_name, last_name, phone_number, email)
    VALUES (?, ?, 'confirmed', ?, ?, ?, ?)
");
$stmt->bind_param("isssss", $user_id, $flight_id, $first_name, $last_name, $phone, $email);
$stmt->execute();
$booking_id = $stmt->insert_id;

// ✅ STEP 2: ดึงข้อมูลเที่ยวบิน
$stmt = $conn->prepare("
    SELECT 
        Flight.*, 
        Plane.plane_model, 
        Plane.airline,
        dep.airport_name AS from_airport,
        dest.airport_name AS to_airport
    FROM Flight
    JOIN Plane ON Flight.plane_id = Plane.plane_id
    JOIN Airport dep ON Flight.airport_departure_id = dep.airport_id
    JOIN Airport dest ON Flight.airport_destination_id = dest.airport_id
    WHERE flight_id = ?
");
$stmt->bind_param("s", $flight_id);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Booking Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-50 to-blue-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-3xl animate-fade-in-down">
        <h2 class="text-3xl font-bold text-blue-700 text-center mb-4">🎟️ ยืนยันการจองสำเร็จ</h2>
        <p class="text-center text-gray-600 mb-6">
            Booking ID: <span class="font-semibold text-gray-800"><?= $booking_id ?></span>
        </p>

        <!-- Flight Detail Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6 shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold text-blue-800 mb-2"><?= htmlspecialchars($flight['airline']) ?> - <?= htmlspecialchars($flight['plane_model']) ?></h3>
            <p class="text-gray-700 mb-1">
                ✈️ <strong><?= htmlspecialchars($flight['from_airport']) ?></strong>
                → <strong><?= htmlspecialchars($flight['to_airport']) ?></strong>
            </p>
            <p class="text-gray-600 mb-1">
                🕒 เวลาเดินทาง: <strong><?= $flight['flight_time'] ?></strong> |
                📅 วันที่: <strong><?= $flight['date'] ?></strong>
            </p>
            <p class="text-green-600 font-bold text-lg">
                💵 ราคา: ฿<?= number_format($flight['price'], 2) ?>
            </p>
        </div>

        <!-- Passenger Info -->
        <div class="mb-6 bg-white border border-gray-300 rounded-xl p-4 shadow">
            <h4 class="text-lg font-semibold text-gray-700 mb-2">👤 ผู้โดยสาร</h4>
            <p><b>ชื่อ:</b> <?= htmlspecialchars($first_name . ' ' . $last_name) ?></p>
            <p><b>โทร:</b> <?= htmlspecialchars($phone) ?></p>
            <p><b>อีเมล:</b> <?= htmlspecialchars($email) ?></p>
        </div>

        <!-- Ticket Generator -->
        <div class="pt-4 border-t border-gray-200">
            <?php
            $_POST['booking_id'] = $booking_id;
            $_POST['passenger_id'] = $passenger_id;
            $_POST['flight_id'] = $flight_id;
            $_POST['seat_no'] = $seat_no;
            include('ticket_generator.php');
            ?>
        </div>

        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="index.php"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-full shadow-lg transition duration-300">
                🔙 กลับหน้าแรก
            </a>
        </div>
    </div>

    <style>
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.6s ease-out;
        }
    </style>
</body>
</html>
