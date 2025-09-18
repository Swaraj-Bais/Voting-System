<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$usernameOrEmail = isset($_POST['username']) ? trim($_POST['username']) : '';
	$password = isset($_POST['password']) ? trim($_POST['password']) : '';

	if ($usernameOrEmail === '' || $password === '') {
		die("All fields are required.");
	}

	// Admin short-circuit
	$ADMIN_USERNAME = 'HeadAdmin';
	$ADMIN_PASSWORD = 'mathematics';
	if ($usernameOrEmail === $ADMIN_USERNAME && $password === $ADMIN_PASSWORD) {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		$_SESSION['user_id'] = 0;
		$_SESSION['username'] = $ADMIN_USERNAME;
		$_SESSION['email'] = '';
		$_SESSION['role'] = 'admin';
		header("Location: ../../frontend/dashboard/view/index.php");
		exit;
	}

	// Database connection parameters
	$host = "127.0.0.1";
	$user = "root";
	$pass = "mathematics";
	$db = "VotingSys"; // Ensure exact case match
	$port = 3307;

	$conn = new mysqli($host, $user, $pass, $db, $port);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	// Determine whether input is email or username
	$isEmail = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL) !== false;
	$query = $isEmail
		? "SELECT id, username, email, password FROM users WHERE email = ? LIMIT 1"
		: "SELECT id, username, email, password FROM users WHERE username = ? LIMIT 1";

	$stmt = $conn->prepare($query);
	if (!$stmt) {
		die("Prepare failed: " . $conn->error);
	}

	$stmt->bind_param("s", $usernameOrEmail);
	if (!$stmt->execute()) {
		$stmt->close();
		$conn->close();
		die("Login failed. Please try again.");
	}

	$result = $stmt->get_result();
	$userRow = $result ? $result->fetch_assoc() : null;
	$stmt->close();

	if (!$userRow) {
		$conn->close();
		die("Invalid credentials.");
	}

	if (!password_verify($password, $userRow['password'])) {
		$conn->close();
		die("Invalid credentials.");
	}

	// Successful authentication: start session and redirect
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	$_SESSION['user_id'] = $userRow['id'];
	$_SESSION['username'] = $userRow['username'];
	$_SESSION['email'] = $userRow['email'];
	$_SESSION['role'] = 'user';

	$conn->close();
	header("Location: ../../frontend/vote/index.php");
	exit;
} else {
	echo "Please submit the form.";
}
?>
