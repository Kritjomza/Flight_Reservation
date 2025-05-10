<?php
$timeout = 900; // 15 นาที
if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > $timeout) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();
?>

<nav class="fixed top-0 left-0 w-full bg-white/90 backdrop-blur-md shadow-md z-50 border-b border-gray-200">
  <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="w-full py-4 flex justify-between items-center">

      <!-- Logo -->
      <a href="index.php" class="flex items-center space-x-3">
        <img src="./img/logo.png" alt="Logo" class="w-10 h-10 object-contain drop-shadow" />
        <span class="text-xl font-bold text-blue-700 tracking-wide">Booking</span>
      </a>

      <!-- Navigation -->
      <?php if (!isset($_SESSION['user_id'])): ?>
      <div class="flex space-x-2">
        <a href="register.php"
           class="px-5 py-2 border border-blue-600 text-blue-600 font-medium text-sm md:text-base rounded-md hover:bg-blue-600 hover:text-white transition">
          Sign Up
        </a>
        <a href="login.php"
           class="px-5 py-2 bg-blue-600 text-white font-medium text-sm md:text-base rounded-md hover:bg-blue-700 transition">
          Log In
        </a>
      </div>
      <?php else: ?>
      <!-- Dropdown Menu (Logged In) -->
      <div class="relative">
        <button id="menu-btn" class="flex items-center text-blue-700 hover:text-blue-900 transition">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
               viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <!-- Floating Menu -->
        <div id="menu-card"
             class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl p-4 animate-fade-in z-50">
          <h3 class="text-lg font-semibold text-blue-600 mb-3">เมนูผู้ใช้</h3>
          <div class="space-y-2">
            <a href="profile.php"
               class="block px-4 py-2 rounded-md hover:bg-blue-100 text-blue-700 font-medium">จัดการโปรไฟล์</a>
            <a href="my_tickets.php"
               class="block px-4 py-2 rounded-md hover:bg-blue-100 text-blue-700 font-medium">ตั๋วของฉัน</a>
            <a href="logout.php"
               class="block px-4 py-2 rounded-md bg-red-100 hover:bg-red-200 text-red-700 font-medium">ออกจากระบบ</a>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Script -->
<script>
  const menuBtn = document.getElementById('menu-btn');
  const menuCard = document.getElementById('menu-card');

  if (menuBtn && menuCard) {
    menuBtn.addEventListener('click', () => {
      menuCard.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!menuCard.contains(e.target) && !menuBtn.contains(e.target)) {
        menuCard.classList.add('hidden');
      }
    });
  }
</script>

<!-- Animation Style -->
<style>
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-fade-in {
    animation: fadeIn 0.25s ease-out;
  }
</style>
