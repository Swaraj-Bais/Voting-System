<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
  

    if (empty($username) || empty($password) || empty($email)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Database connection parameters
    $host = "127.0.0.1";
    $user = "root";
    $pass = "mathematics";
    $db = "VotingSys"; // Ensure exact case match
    $port = 3307;

    // Proper mysqli constructor order: host, username, password, database, port
    $conn = new mysqli($host, $user, $pass, $db, $port);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $username, $hashedPassword, $email);

    if ($stmt->execute()) {
        echo "New user registered successfully!";
        header("Location: ../../frontend/login/index.html");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Please submit the form.";
}
?>
