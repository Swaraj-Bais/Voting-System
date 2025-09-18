<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("Location: ../login/index.html");
	exit;
}
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
	header("Location: ../dashboard/view/index.php");
	exit;
}

// Database connection
$host = "127.0.0.1";
$user = "root";
$pass = "mathematics";
$db = "VotingSys";
$port = 3307;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

// Ensure user_votes table exists (id, user_id, party_id, created_at)
$conn->query("CREATE TABLE IF NOT EXISTS user_votes (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL UNIQUE, party_id INT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

$userId = intval($_SESSION['user_id']);

// Check if user already voted
$alreadyVoted = false;
$stmt = $conn->prepare("SELECT party_id FROM user_votes WHERE user_id = ? LIMIT 1");
if ($stmt) {
	$stmt->bind_param("i", $userId);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($res && $res->num_rows > 0) {
		$alreadyVoted = true;
	}
	$stmt->close();
}

// Get parties list
$parties = [];
$res = $conn->query("SELECT id, party, leader, image FROM parties ORDER BY id ASC");
if ($res) {
	while ($row = $res->fetch_assoc()) {
		$parties[] = $row;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Vote for Your Preferred Party</h1>
        <p>Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <a href="../dashboard/view/logout.php">Logout</a>
        <?php if ($alreadyVoted) { ?>
            <div class="notice">You have already voted. Thank you!</div>
        <?php } else { ?>
            <form method="post" action="vote.php">
                <div class="parties">
                    <?php foreach ($parties as $p) { ?>
                        <label class="party-card">
                            <input type="radio" name="party_id" value="<?php echo intval($p['id']); ?>" required>
                            <div class="party-info">
                                <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="Party Image">
                                <div>
                                    <div class="party-name"><?php echo htmlspecialchars($p['party']); ?></div>
                                    <div class="leader-name">Leader: <?php echo htmlspecialchars($p['leader']); ?></div>
                                </div>
                            </div>
                        </label>
                    <?php } ?>
                </div>
                <button type="submit">Submit Vote</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>

