<?php
session_start();
include('server.php');
include 'navbar.php';

// เช็คการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึง passenger_id จาก user
$stmt = $conn->prepare("SELECT passenger_id FROM Passenger WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("ไม่พบข้อมูลผู้โดยสาร");
}
$passenger_id = $row['passenger_id'];

// ดึงข้อมูลตั๋วทั้งหมดของผู้ใช้
$stmt = $conn->prepare("
    SELECT 
        T.ticket_id,
        T.seat_no,
        T.status AS ticket_status,
        F.flight_id,
        F.date,
        F.flight_time,
        F.price,
        P.plane_model,
        P.airline,
        A1.airport_name AS departure_airport,
        A2.airport_name AS destination_airport
    FROM Ticket T
    JOIN Flight F ON T.flight_id = F.flight_id
    JOIN Plane P ON F.plane_id = P.plane_id
    JOIN Airport A1 ON F.airport_departure_id = A1.airport_id
    JOIN Airport A2 ON F.airport_destination_id = A2.airport_id
    WHERE T.passenger_id = ?
    ORDER BY F.date DESC
");
$stmt->bind_param("i", $passenger_id);
$stmt->execute();
$tickets = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ตั๋วของฉัน</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen pt-24">
    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-xl p-6">
        <h2 class="text-2xl font-bold text-blue-700 mb-6 text-center">🎟️ ตั๋วของฉัน</h2>

        <?php if ($tickets->num_rows > 0): ?>
            <div class="space-y-4">
                <?php while ($row = $tickets->fetch_assoc()): ?>
                    <div class="border border-gray-200 rounded-xl p-4 shadow hover:shadow-md transition bg-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-blue-800"><?= $row['airline'] ?> - <?= $row['plane_model'] ?></h3>
                                <p class="text-gray-600 text-sm">
                                    ✈️ <?= $row['departure_airport'] ?> → <?= $row['destination_airport'] ?>
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    📅 <?= $row['date'] ?> | 🕒 <?= $row['flight_time'] ?> | 💺 ที่นั่ง: <strong><?= $row['seat_no'] ?></strong>
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-green-600 font-semibold">฿<?= number_format($row['price'], 2) ?></div>
                                <div class="text-sm text-gray-500"><?= ucfirst($row['ticket_status']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">คุณยังไม่มีตั๋ว</p>
        <?php endif; ?>

        <div class="mt-8 text-center">
            <a href="index.php"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-full shadow">
               🔙 กลับหน้าแรก
            </a>
        </div>
    </div>
</body>
</html>
