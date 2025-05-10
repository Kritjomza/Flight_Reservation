<?php  
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit();
}

include 'server.php';
include 'navbar.php';

// ตรวจLogout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$airports_domestic = [];
$airports_international = [];

$query = "SELECT * FROM Airport"; 
$result = mysqli_query($conn, $query); 

while ($row = mysqli_fetch_assoc($result)) { 
    if ($row['country'] == 'Thailand') {
        $airports_domestic[] = $row;
        
        if (in_array($row['airport_name'], ['Suvarnabhumi Airport', 'Don Mueang International Airport'])) {
            $airports_international[] = $row;
        }

    } else {  
        $airports_international[] = $row; 
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agado - Flight Booking</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
        /* body {
            background-image: url('./img/bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        } */

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-cover bg-center" style="background-image: url('./img/bg.png')">


    <!-- Booking Form -->
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl p-8 space-y-6 fade-in-up">

    <!-- ปุ่มเลือกประเภทเที่ยวบิน -->
    <div class="flex justify-center gap-2">
    <button id="btnDomestic" onclick="setFlightType('Domestic')"
        class="px-5 py-2 rounded-full text-sm font-semibold transition active-tab bg-blue-600 text-white shadow-md">
        Domestic
    </button>
    <button id="btnInternational" onclick="setFlightType('International')"
        class="px-5 py-2 rounded-full text-sm font-semibold transition inactive-tab bg-gray-100 text-gray-800">
        International
    </button>
    </div>

    <!-- ฟอร์มจอง -->
    <form action="search_results.php" method="GET" class="space-y-4">
    <input type="hidden" id="flightType" name="flightType" value="Domestic" />

    <!-- From -->
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2">
        <img src="./img/from.png" class="w-5 h-5">
        </span>
        <select id="from" name="from" required
        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none">
        </select>
    </div>

    <!-- To -->
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2">
        <img src="./img/to.png" class="w-5 h-5">
        </span>
        <select id="to" name="to" required
        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none">
        </select>
    </div>

    <!-- Date -->
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2">
        <img src="./img/date.png" class="w-5 h-5">
        </span>
        <input type="date" name="departure" required
        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none" />
    </div>

    <!-- Passengers -->
    <!-- <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2">
        <img src="./img/passenger.png" class="w-5 h-5">
        </span>
        <select name="passengers" required
        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none">
        <option value="1">1 Passenger</option>
        <option value="2">2 Passengers</option>
        <option value="3">3 Passengers</option>
        </select>
    </div> -->

    <!-- ปุ่มค้นหา -->
    <button type="submit"
        class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
        SEARCH FLIGHTS
    </button>
    </form>
    </div>


    <script>
        let domesticAirports = <?php echo json_encode($airports_domestic); ?>;
        let internationalAirports = <?php echo json_encode($airports_international); ?>;

        function loadAirports(type) {
            let fromSelect = document.getElementById('from');
            let toSelect = document.getElementById('to');

            fromSelect.innerHTML = "";
            toSelect.innerHTML = "";

            let selectedAirports = type === 'Domestic' ? domesticAirports : internationalAirports;

            selectedAirports.forEach(airport => {
                let option = `<option value="${airport.airport_id}">${airport.airport_name} (${airport.city}, ${airport.country})</option>`;
                fromSelect.innerHTML += option;
                toSelect.innerHTML += option;
            });
        }

        function setFlightType(type) {
            document.getElementById('flightType').value = type;
            document.getElementById('btnDomestic').classList.toggle('bg-blue-600', type === 'Domestic');
            document.getElementById('btnDomestic').classList.toggle('text-white', type === 'Domestic');
            document.getElementById('btnDomestic').classList.toggle('bg-gray-100', type !== 'Domestic');
            document.getElementById('btnDomestic').classList.toggle('text-gray-800', type !== 'Domestic');

            document.getElementById('btnInternational').classList.toggle('bg-blue-600', type === 'International');
            document.getElementById('btnInternational').classList.toggle('text-white', type === 'International');
            document.getElementById('btnInternational').classList.toggle('bg-gray-100', type !== 'International');
            document.getElementById('btnInternational').classList.toggle('text-gray-800', type !== 'International');

            loadAirports(type);
        }

        document.addEventListener("DOMContentLoaded", function () {
            loadAirports("Domestic");
        });
    </script>

</body>
</html>
