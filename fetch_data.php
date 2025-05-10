<?php
include 'server.php';

if (isset($_GET['table'])) {
    $table = $_GET['table'];

    $custom_queries = [
        'popular_flights_this_month',
        'empty_flights',
        'top_flight_this_month',
        'daily_revenue',
        'flight_with_airport',
        'passenger_summary'
    ];

    if (in_array($table, $custom_queries)) {
        switch ($table) {

            // เพิ่ม flight ยอดนิยมในเดือนนี้ (JOIN + GROUP BY)
            case 'popular_flights_this_month':
                $sql = "SELECT t.flight_id, COUNT(*) AS ticket_count
                        FROM Ticket t
                        JOIN Booking b ON t.booking_id = b.booking_id
                        WHERE MONTH(b.booking_date) = MONTH(CURDATE())
                        GROUP BY t.flight_id
                        ORDER BY ticket_count DESC";
                break;
            

                // จำนวนเที่ยวบินที่ว่าง (ไม่มีคนจองเลย) (Subquery)
            case 'empty_flights':
                $sql = "SELECT f.flight_id, f.date, f.flight_time
                        FROM Flight f
                        WHERE f.flight_id NOT IN (
                            SELECT DISTINCT flight_id FROM Ticket
                        )";
                break;


                // เที่ยวบินที่มีผู้โดยสารมากที่สุดในเดือนปัจจุบัน (SUBQUERY)
            case 'top_flight_this_month':
                $sql = "SELECT t.flight_id, COUNT(*) AS passenger_count
                        FROM Ticket t
                        JOIN Booking b ON t.booking_id = b.booking_id
                        WHERE MONTH(b.booking_date) = MONTH(CURDATE()) AND YEAR(b.booking_date) = YEAR(CURDATE())
                        GROUP BY t.flight_id
                        ORDER BY passenger_count DESC
                        LIMIT 1";
                break;


                // สรุปรายได้รวมจากการจองในแต่ละวัน (JOIN + GROUP BY + DATE)
            case 'daily_revenue':
                $sql = "SELECT DATE(p.payment_date) AS date, SUM(p.amount) AS total_income
                        FROM Payment p
                        JOIN Booking b ON p.booking_id = b.booking_id
                        GROUP BY DATE(p.payment_date)
                        ORDER BY date DESC";
                break;

                // เที่ยวบินพร้อมข้อมูลสนามบินต้นทางและปลายทาง (JOIN)
            case 'flight_with_airport':
                $sql = "SELECT f.flight_id, 
                            a1.airport_name AS departure_airport, 
                            a2.airport_name AS arrival_airport, 
                            f.date, f.flight_time
                        FROM Flight f
                        JOIN Airport a1 ON f.airport_departure_id = a1.airport_id
                        JOIN Airport a2 ON f.airport_destination_id = a2.airport_id";
                break;


                // ผู้โดยสารแต่ละคน เคยเดินทางทั้งหมดกี่ครั้ง (JOIN + GROUP BY)
            case 'passenger_summary':  
                $sql = "SELECT p.passenger_id, 
                            CONCAT(p.first_name, ' ', p.last_name) AS full_name, 
                            COUNT(t.ticket_id) AS total_flights
                        FROM Passenger p
                        LEFT JOIN Ticket t ON p.passenger_id = t.passenger_id
                        GROUP BY p.passenger_id, full_name";
                break;

        }

        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            echo "<div class='overflow-x-auto'>";
            echo "<table class='min-w-full table-auto border border-gray-300 text-sm text-gray-700'>";
            echo "<thead class='bg-gray-200'><tr>";
            while ($field = $result->fetch_field()) {
                echo "<th class='border border-gray-300 px-4 py-2 text-left'>{$field->name}</th>";
            }
            echo "</tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr class='hover:bg-gray-100'>";
                foreach ($row as $cell) {
                    echo "<td class='border border-gray-200 px-4 py-2 break-all'>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "<p class='text-red-500'>ไม่มีข้อมูล</p>";
        }

        exit(); // << สำคัญ! หยุดการทำงานหลังแสดงผล
    }

    // ถ้าไม่ใช่ custom query ใช้ตรงนี้
    $allowed_tables = ['Passenger', 'airport', 'ticket', 'payment', 'booking'];
    if (!in_array($table, $allowed_tables)) {
        echo "<p class='text-red-500'>ไม่อนุญาตให้เข้าถึงตารางนี้</p>";
        exit();
    }

    if ($table == 'airport' && isset($_GET['type'])) {
        $type = $_GET['type'];
        if ($type === 'domestic') {
            $sql = "SELECT * FROM Airport WHERE country = 'Thailand'";
        } elseif ($type === 'international') {
            $sql = "SELECT * FROM Airport WHERE country != 'Thailand' OR airport_name IN ('Suvarnabhumi Airport', 'Don Mueang International Airport')";
        } else {
            echo "<p class='text-red-500'>ประเภทสนามบินไม่ถูกต้อง</p>";
            exit();
        }
    } else {
        $sql = "SELECT * FROM $table";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<div class='overflow-x-auto'>";
        echo "<table class='min-w-full table-auto border border-gray-300 text-sm text-gray-700'>";
        echo "<thead class='bg-gray-200'><tr>";
        while ($field = $result->fetch_field()) {
            echo "<th class='border border-gray-300 px-4 py-2 text-left'>{$field->name}</th>";
        }
        echo "</tr></thead><tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr class='hover:bg-gray-100'>";
            foreach ($row as $cell) {
                echo "<td class='border border-gray-200 px-4 py-2 break-all'>$cell</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table></div>";
    } else {
        echo "<p class='text-red-500'>ไม่มีข้อมูลในตารางนี้</p>";
    }
}
?>
