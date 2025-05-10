<div id="sidebar"
     class="fixed top-0 left-0 h-screen w-64 bg-white shadow-xl flex flex-col justify-between 
            transform -translate-x-full md:translate-x-0 md:relative md:flex transition-transform duration-300 ease-in-out z-50">

    <!-- Logo -->
    <div>
        <div class="flex items-center space-x-3 p-5 border-b border-gray-200">
            <img src="./img/logo.png" class="w-12 h-12" alt="logo">
            <h1 class="text-2xl font-bold text-indigo-600">Agado</h1>
        </div>

        <!-- Menu -->
        <nav class="p-5">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">📁 Menu</h2>
            <ul class="space-y-2 text-gray-600">

                <li>
                    <button onclick="loadTable('Passenger')" class="nav-btn flex items-center space-x-2">
                        <i class="ph ph-user"></i><span>Passenger</span>
                    </button>
                </li>

                <li>
                    <button onclick="toggleDropdown()" class="nav-btn flex items-center space-x-2">
                        <i class="ph ph-airplane"></i><span>Airport</span><span class="ml-auto">▼</span>
                    </button>
                    <ul id="ticket-dropdown" class="ml-5 hidden space-y-2 mt-2">
                        <li><button onclick="loadAirportType('domestic')" class="nav-btn">Domestic Airport</button></li>
                        <li><button onclick="loadAirportType('international')" class="nav-btn">International Airport</button></li>

                    </ul>
                </li>

                

                <li>
                    <button onclick="loadTable('ticket')" class="nav-btn flex items-center space-x-2">
                        <i class="ph ph-ticket"></i><span>Tickets</span>
                    </button>
                </li>

                <li>
                    <button onclick="loadTable('payment')" class="nav-btn flex items-center space-x-2">
                        <i class="ph ph-credit-card"></i><span>Payment</span>
                    </button>
                </li>

                <li>
                    <button onclick="loadTable('booking')" class="nav-btn flex items-center space-x-2">
                        <i class="ph ph-calendar-check"></i><span>Bookings</span>
                    </button>
                </li>

                <li>
                    <button onclick="toggleStatsDropdown()" class="nav-btn flex items-center space-x-2">
                        <i class="ph ph-chart-bar"></i><span>Statistics</span><span class="ml-auto">▼</span>
                    </button>
                    <ul id="stats-dropdown" class="ml-5 hidden space-y-2 mt-2">
                        <li><button onclick="loadTable('popular_flights_this_month')" class="nav-btn">Ticket Flight</button></li>
                        <li><button onclick="loadTable('empty_flights')" class="nav-btn">Empty Flight</button></li>
                        <li><button onclick="loadTable('top_flight_this_month')" class="nav-btn">Top Flight</button></li>
                        <li><button onclick="loadTable('daily_revenue')" class="nav-btn">Daily Revenue</button></li>
                        <li><button onclick="loadTable('flight_with_airport')" class="nav-btn">Flight And Airport</button></li>
                        <li><button onclick="loadTable('passenger_summary')" class="nav-btn">Passenger Summary</button></li>
                    </ul>
                </li>

                <li>
                    <a href="manage_flights.php" class="nav-btn flex items-center space-x-2 text-indigo-600 font-medium hover:bg-indigo-50">
                        <i class="ph ph-wrench"></i><span>Manage Flights</span>
                    </a>
                </li>
                <li>
                    <a href="manage_passenger.php" class="nav-btn flex items-center space-x-2 text-indigo-600 font-medium hover:bg-indigo-50">
                        <i class="ph ph-wrench"></i><span>Manage Passenger</span>
                    </a>
                </li>


            </ul>
        </nav>
    </div>

    <!-- Logout -->
    <div class="p-5 border-t border-gray-200">
        <a href="?logout=true" class="block text-center bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
            <i class="ph ph-sign-out mr-2"></i>Logout
        </a>
    </div>
</div>

<script>
function loadAirportType(type) {
    fetch(`fetch_data.php?table=airport&type=${type}`)
        .then(res => res.text())
        .then(html => document.getElementById('main-content').innerHTML = html);
}
</script>