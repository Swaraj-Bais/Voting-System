<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("Location: ../login/index.html");
	exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	die('Invalid request');
}

$userId = intval($_SESSION['user_id']);
$partyId = isset($_POST['party_id']) ? intval($_POST['party_id']) : 0;
if ($partyId <= 0) {
	die('Please select a party.');
}

// DB
$host = "127.0.0.1";
$user = "root";
$pass = "mathematics";
$db = "VotingSys";
$port = 3307;
$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

// ensure table
$conn->query("CREATE TABLE IF NOT EXISTS user_votes (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL UNIQUE, party_id INT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

// begin transaction to avoid double votes under race
$conn->begin_transaction();
try {
	// check existing vote
	$check = $conn->prepare("SELECT id FROM user_votes WHERE user_id = ? LIMIT 1");
	$check->bind_param("i", $userId);
	$check->execute();
	$r = $check->get_result();
	if ($r && $r->num_rows > 0) {
		$check->close();
		$conn->rollback();
		$conn->close();
		header("Location: index.php");
		exit;
	}
	$check->close();

	// insert vote
	$ins = $conn->prepare("INSERT INTO user_votes (user_id, party_id) VALUES (?, ?)");
	$ins->bind_param("ii", $userId, $partyId);
	$ins->execute();
	$ins->close();

	// increment party votes
	$upd = $conn->prepare("UPDATE parties SET votes = votes + 1 WHERE id = ?");
	$upd->bind_param("i", $partyId);
	$upd->execute();
	$upd->close();

	$conn->commit();
} catch (Exception $e) {
	$conn->rollback();
}
$conn->close();
header("Location: index.php");
exit;
?>

