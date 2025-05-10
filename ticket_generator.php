<?php
include('server.php'); // เชื่อมต่อฐานข้อมูล

// รับข้อมูลจาก POST หรือ GET (ควรใช้ POST จริงๆ)
$booking_id = $_POST['booking_id'] ?? null;
$passenger_id = $_POST['passenger_id'] ?? null;
$flight_id = $_POST['flight_id'] ?? null;

if (!$booking_id || !$passenger_id || !$flight_id) {
    die("กรุณาระบุ booking_id, passenger_id และ flight_id");
}

// ฟังก์ชันสุ่มเลขที่นั่ง
function generateSeatNumber($conn, $flight_id) {
    $rows = range(1, 30); // แถว 1 - 30
    $cols = ['A', 'B', 'C', 'D', 'E', 'F']; // ที่นั่ง A - F

    do {
        $seat = $rows[array_rand($rows)] . $cols[array_rand($cols)];

        // ตรวจสอบว่า seat ซ้ำหรือยัง
        $stmt = $conn->prepare("SELECT * FROM Ticket WHERE flight_id = ? AND seat_no = ?");
        $stmt->bind_param("ss", $flight_id, $seat);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0); // ถ้าซ้ำให้สุ่มใหม่

    return $seat;
}

// สร้างหมายเลขที่นั่ง
$seat_no = generateSeatNumber($conn, $flight_id);

// เพิ่มข้อมูลตั๋ว
$stmt = $conn->prepare("INSERT INTO Ticket (booking_id, passenger_id, flight_id, seat_no, status) VALUES (?, ?, ?, ?, 'booked')");
$stmt->bind_param("iiss", $booking_id, $passenger_id, $flight_id, $seat_no);

if ($stmt->execute()) {
    echo "<h3>สร้างตั๋วสำเร็จ!</h3>";
    echo "หมายเลขที่นั่งของคุณคือ: <strong>$seat_no</strong>";
} else {
    echo "เกิดข้อผิดพลาดในการสร้างตั๋ว: " . $stmt->error;
}
?>
