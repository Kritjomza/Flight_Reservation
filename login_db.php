<?php
session_start();
include('server.php');

$errors = array();

if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) === 0) {
        $stmt = $conn->prepare("SELECT * FROM User WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true); // ป้องกัน Session Fixation
                $_SESSION['user_id'] = $user['user_id']; // <-- บรรทัดสำคัญ
                $_SESSION['user_logged_in'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['success'] = "เข้าสู่ระบบสำเร็จ!";
            
                if ($user['role'] === 'passenger') {
                    header("location: index.php");
                } else {
                    header("location: admin_dashboard.php");
                }
                exit();
                
            }
             else {
                $_SESSION['error'] = "รหัสผ่านไม่ถูกต้อง!";
                header("location: login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "ไม่พบชื่อผู้ใช้นี้!";
            header("location: login.php");
            exit();
        }
    }

    // กลับไปที่หน้า login.php และแสดงแจ้งเตือน
    
    exit();
}
?>
