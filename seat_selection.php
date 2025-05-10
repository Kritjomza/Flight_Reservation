<?php
include('server.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อนเลือกที่นั่ง");
}

$flight_id = $_GET['flight_id'] ?? null;
if (!$flight_id) {
    die("ไม่พบรหัสเที่ยวบิน");
}

// ดึงข้อมูลที่นั่งที่จองไปแล้ว
$query = $conn->prepare("SELECT seat_no FROM Ticket WHERE flight_id = ? AND status = 'booked'");
$query->bind_param("s", $flight_id);
$query->execute();
$result = $query->get_result();

$booked_seats = [];
while ($row = $result->fetch_assoc()) {
    $booked_seats[] = $row['seat_no'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>เลือกที่นั่ง</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .seat {
            width: 40px; height: 40px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin: 4px;
            text-align: center;
            line-height: 40px;
            cursor: pointer;
        }
        .booked {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .selected {
            background-color: #4f46e5;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <h2 class="text-2xl font-bold mb-6">เลือกที่นั่งสำหรับเที่ยวบิน <?= htmlspecialchars($flight_id) ?></h2>

    <form method="POST" action="booking.php">
        <input type="hidden" name="flight_id" value="<?= $flight_id ?>">
        <input type="hidden" name="seat_no" id="seatInput">

        <div class="grid grid-cols-6 gap-2 mb-6">
            <?php
            $rows = range(1, 10);
            $cols = ['A', 'B', 'C', 'D', 'E', 'F'];

            foreach ($rows as $rowNum) {
                foreach ($cols as $col) {
                    $seat = $rowNum . $col;
                    $booked = in_array($seat, $booked_seats);
                    $class = $booked ? 'booked' : 'seat';
                    echo "<div class='seat $class' data-seat='$seat' " . ($booked ? "style='pointer-events: none;'" : '') . ">$seat</div>";
                }
            }
            ?>
        </div>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded"
                onclick="return validateSeat()">ยืนยันที่นั่ง</button>
    </form>

    <script>
        const seats = document.querySelectorAll('.seat:not(.booked)');
        let selected = null;

        seats.forEach(seat => {
            seat.addEventListener('click', () => {
                if (selected) selected.classList.remove('selected');
                seat.classList.add('selected');
                selected = seat;
                document.getElementById('seatInput').value = seat.dataset.seat;
            });
        });

        function validateSeat() {
            if (!document.getElementById('seatInput').value) {
                alert('กรุณาเลือกที่นั่งก่อน');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
