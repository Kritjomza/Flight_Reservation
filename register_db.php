<?php
session_start();
include('server.php');

$errors = array();

if (isset($_POST['reg_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_1 = $_POST['password1'];
    $password_2 = $_POST['password2'];
    
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $citizen_id = trim($_POST['citizen_id']);
    $passport_id = !empty($_POST['passport_id']) ? trim($_POST['passport_id']) : null;
    $gender = $_POST['gender'];

    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($email)) { array_push($errors, "Email is required"); }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { array_push($errors, "Invalid email format"); }
    if (empty($password_1)) { array_push($errors, "Password is required"); }
    if ($password_1 !== $password_2) { array_push($errors, "The two passwords do not match"); }

    if (count($errors) == 0) {
        $stmt = $conn->prepare("SELECT * FROM User WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($user['username'] === $username) { array_push($errors, "Username already exists"); }
            if ($user['email'] === $email) { array_push($errors, "Email already exists"); }
        }
    }

    if (count($errors) == 0) {
        $hashed_password = password_hash($password_1, PASSWORD_DEFAULT);

        // เพิ่มผู้ใช้ลงใน User
        $stmt = $conn->prepare("INSERT INTO User (username, email, password, role) VALUES (?, ?, ?, 'passenger')");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt->close();

            // เพิ่มข้อมูลลงใน Passenger
            $stmt = $conn->prepare("INSERT INTO Passenger (user_id, first_name, last_name, citizen_id, passport_id, gender) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $user_id, $first_name, $last_name, $citizen_id, $passport_id, $gender);
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['success'] = "You are now logged in";
                header('location: index.php');
                exit();
            } else {
                array_push($errors, "Failed to insert passenger information.");
            }
            $stmt->close();
        } else {
            array_push($errors, "Registration failed. Please try again.");
        }
    }
}
?>
