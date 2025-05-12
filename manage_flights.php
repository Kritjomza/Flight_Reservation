<?php
require_once 'server.php';
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Search filter
$search = $_GET['search'] ?? '';

// Edit
$editing = false;
$edit_flight = null;
if (isset($_GET['edit'])) {
    $editing = true;
    $edit_id = $_GET['edit'];
    $edit_flight = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Flight WHERE flight_id = '$edit_id'"));
}

// Delete
if (isset($_GET['delete'])) {
    $flight_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM Flight WHERE flight_id = '$flight_id'");
    header("Location: manage_flights.php");
    exit();
}

// Submit Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_id = $_POST['flight_id'];
    $plane_id = $_POST['plane_id'];
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $time = $_POST['flight_time'];
    $date = $_POST['date'];
    $price = $_POST['price'];
    $type = $_POST['flight_type'];

    if (isset($_POST['edit_mode']) && $_POST['edit_mode'] === '1') {
        // Update flight
        mysqli_query($conn, "UPDATE Flight SET 
            plane_id = '$plane_id',
            airport_departure_id = '$departure',
            airport_destination_id = '$destination',
            flight_time = '$time',
            date = '$date',
            price = '$price',
            flight_type = '$type'
            WHERE flight_id = '$flight_id'");
    } else {
        // Add new flight
        mysqli_query($conn, "INSERT INTO Flight (flight_id, plane_id, airport_departure_id, airport_destination_id, flight_time, date, price, flight_type)
                             VALUES ('$flight_id', '$plane_id', '$departure', '$destination', '$time', '$date', '$price', '$type')");
    }

    header("Location: manage_flights.php");
    exit();
}

// Query flights with seat availability
$query = "
    SELECT f.*, 
           p.plane_model, p.capacity,
           a1.airport_name AS depart, 
           a2.airport_name AS arrival,
           (p.capacity - IFNULL(t.seats, 0)) AS available_seats
    FROM Flight f
    JOIN Plane p ON f.plane_id = p.plane_id
    JOIN Airport a1 ON f.airport_departure_id = a1.airport_id
    JOIN Airport a2 ON f.airport_destination_id = a2.airport_id
    LEFT JOIN (
        SELECT flight_id, COUNT(*) AS seats
        FROM Ticket
        WHERE status = 'booked'
        GROUP BY flight_id
    ) t ON f.flight_id = t.flight_id
    WHERE f.flight_id LIKE '%$search%'
       OR a1.airport_name LIKE '%$search%'
       OR a2.airport_name LIKE '%$search%'
    ORDER BY f.date DESC
";
$flights = mysqli_query($conn, $query);

// โหลด dropdown
$planes = mysqli_query($conn, "SELECT * FROM Plane");
$airports = mysqli_query($conn, "SELECT * FROM Airport");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Flights</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">

    <!-- Back to dashboard -->
    <div class="mb-4">
        <a href="admin_dashboard.php" class="inline-flex items-center text-indigo-600 hover:underline">
            <i class="ph ph-arrow-left mr-2"></i> กลับไปหน้า Dashboard
        </a>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <h2 class="text-2xl font-bold text-gray-700">✈️ จัดการเที่ยวบิน</h2>
            
            <!-- Search -->
            <form method="GET" class="flex items-center space-x-2">
                <input type="text" name="search" placeholder="ค้นหาเที่ยวบิน..." value="<?= htmlspecialchars($search) ?>"
                       class="border px-3 py-1 rounded-md focus:outline-none focus:ring w-64">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-1 rounded hover:bg-indigo-700">
                    ค้นหา
                </button>
            </form>
        </div>

        <!-- Flight Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-center">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border px-2 py-2">Flight ID</th>
                        <th class="border px-2 py-2">Plane</th>
                        <th class="border px-2 py-2">From</th>
                        <th class="border px-2 py-2">To</th>
                        <th class="border px-2 py-2">Time</th>
                        <th class="border px-2 py-2">Date</th>
                        <th class="border px-2 py-2">Price</th>
                        <th class="border px-2 py-2">Type</th>
                        <th class="border px-2 py-2">Available</th>
                        <th class="border px-2 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($flights)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border px-2 py-1"><?= $row['flight_id'] ?></td>
                        <td class="border px-2 py-1"><?= $row['plane_model'] ?></td>
                        <td class="border px-2 py-1"><?= $row['depart'] ?></td>
                        <td class="border px-2 py-1"><?= $row['arrival'] ?></td>
                        <td class="border px-2 py-1"><?= $row['flight_time'] ?></td>
                        <td class="border px-2 py-1"><?= $row['date'] ?></td>
                        <td class="border px-2 py-1"><?= number_format($row['price'], 2) ?></td>
                        <td class="border px-2 py-1"><?= $row['flight_type'] ?></td>
                        <td class="border px-2 py-1"><?= $row['available_seats'] ?> / <?= $row['capacity'] ?></td>
                        <td class="border px-2 py-1 flex justify-center space-x-2">
                            <a href="?edit=<?= $row['flight_id'] ?>" class="text-blue-600 hover:text-blue-800">
                                <i class="ph ph-pencil-simple"></i>
                            </a>
                            <a href="?delete=<?= $row['flight_id'] ?>" onclick="return confirm('ลบเที่ยวบินนี้?')" class="text-red-600 hover:text-red-800">
                                <i class="ph ph-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Form -->
        <h3 class="text-lg font-semibold text-gray-700"><?= $editing ? '✏️ แก้ไขเที่ยวบิน' : '➕ เพิ่มเที่ยวบินใหม่' ?></h3>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <input type="hidden" name="edit_mode" value="<?= $editing ? '1' : '0' ?>">
            <input type="text" name="flight_id" class="p-2 border rounded" placeholder="Flight ID" value="<?= $edit_flight['flight_id'] ?? '' ?>" <?= $editing ? 'readonly' : 'required' ?>>
            <select name="plane_id" class="p-2 border rounded" required>
                <option value="">-- เลือกเครื่องบิน --</option>
                <?php mysqli_data_seek($planes, 0); while ($p = mysqli_fetch_assoc($planes)): ?>
                    <option value="<?= $p['plane_id'] ?>" <?= ($edit_flight['plane_id'] ?? '') == $p['plane_id'] ? 'selected' : '' ?>>
                        <?= $p['plane_model'] ?> (<?= $p['plane_id'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
            <select name="departure" class="p-2 border rounded" required>
                <option value="">-- ต้นทาง --</option>
                <?php mysqli_data_seek($airports, 0); while ($a = mysqli_fetch_assoc($airports)): ?>
                    <option value="<?= $a['airport_id'] ?>" <?= ($edit_flight['airport_departure_id'] ?? '') == $a['airport_id'] ? 'selected' : '' ?>>
                        <?= $a['airport_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <select name="destination" class="p-2 border rounded" required>
                <option value="">-- ปลายทาง --</option>
                <?php mysqli_data_seek($airports, 0); while ($a = mysqli_fetch_assoc($airports)): ?>
                    <option value="<?= $a['airport_id'] ?>" <?= ($edit_flight['airport_destination_id'] ?? '') == $a['airport_id'] ? 'selected' : '' ?>>
                        <?= $a['airport_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="time" name="flight_time" class="p-2 border rounded" value="<?= $edit_flight['flight_time'] ?? '' ?>" required>
            <input type="date" name="date" class="p-2 border rounded" value="<?= $edit_flight['date'] ?? '' ?>" required>
            <input type="number" step="0.01" name="price" class="p-2 border rounded" placeholder="ราคา" value="<?= $edit_flight['price'] ?? '' ?>" required>
            <select name="flight_type" class="p-2 border rounded" required>
                <option value="domestic" <?= ($edit_flight['flight_type'] ?? '') == 'domestic' ? 'selected' : '' ?>>ภายในประเทศ</option>
                <option value="international" <?= ($edit_flight['flight_type'] ?? '') == 'international' ? 'selected' : '' ?>>ระหว่างประเทศ</option>
            </select>
            <button type="submit" class="p-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 col-span-full md:col-span-1">
                <?= $editing ? 'อัปเดตเที่ยวบิน' : 'บันทึกเที่ยวบิน' ?>
            </button>
        </form>
    </div>

</body>
</html>
