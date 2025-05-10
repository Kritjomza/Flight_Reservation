<?php
include 'server.php';

$from = $_GET['from'];
$to = $_GET['to'];
$departure_date = $_GET['departure'];
// $passengers = $_GET['passengers'];
$passengers = 1;

// ดึงเที่ยวบินที่ยังมีที่นั่งว่าง
$query = "
    SELECT 
        Flight.flight_id, 
        Flight.flight_time, 
        Flight.date, 
        Flight.price, 
        Plane.plane_model, 
        Plane.airline, 
        Plane.capacity,
        dep_airport.airport_name AS departure_airport, 
        dest_airport.airport_name AS destination_airport,
        (Plane.capacity - IFNULL(COUNT(Ticket.ticket_id), 0)) AS available_seats
    FROM Flight
    JOIN Plane ON Flight.plane_id = Plane.plane_id
    JOIN Airport AS dep_airport ON Flight.airport_departure_id = dep_airport.airport_id
    JOIN Airport AS dest_airport ON Flight.airport_destination_id = dest_airport.airport_id
    LEFT JOIN Ticket ON Flight.flight_id = Ticket.flight_id AND Ticket.status = 'booked'
    WHERE Flight.airport_departure_id = ?
      AND Flight.airport_destination_id = ?
      AND Flight.date = ?
    GROUP BY Flight.flight_id
    HAVING available_seats >= ?
    ORDER BY Flight.price ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iisi", $from, $to, $departure_date, $passengers);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Flight Search Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="max-w-4xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Search Results</h2>
        <p class="mb-6 text-gray-600">
            Showing flights from <b><?= $from ?></b> to <b><?= $to ?></b> on <b><?= $departure_date ?></b>
        </p>

        <div class="space-y-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="bg-white rounded-xl shadow-md p-5 flex justify-between items-center">
                        <div>
                            <div class="text-lg font-semibold text-blue-600">
                                <?= $row['airline'] ?> - <?= $row['plane_model'] ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?= $row['departure_airport'] ?> → <?= $row['destination_airport'] ?>
                            </div>
                            <div class="mt-1 text-sm">
                                <b>Time:</b> <?= $row['flight_time'] ?> | 
                                <b>Date:</b> <?= $row['date'] ?> |
                                <b>Available Seats:</b> <?= $row['available_seats'] ?>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-green-600 mb-2">
                                ฿ <?= number_format($row['price'], 2) ?>
                            </div>
                            <form method="POST" action="booking.php">
                                <input type="hidden" name="flight_id" value="<?= $row['flight_id'] ?>">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow">
                                    Select
                                </button>
                            </form>

                        </div>
                    </div>
                <?php } ?>
            <?php else: ?>
                <div class="text-center text-gray-600">
                    No available flights found for your search.
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
