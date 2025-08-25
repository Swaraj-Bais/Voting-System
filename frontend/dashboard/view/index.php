<?php
// Database connection
$host = "127.0.0.1";
$user = "root";
$pass = "mathematics";
$db = "VotingSys";
$port = 3307;

// Create connection
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all parties from the database
$sql = "SELECT * FROM parties";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div id="main">
        <div id="left">
            <div id="head">
                <h1>Voting System</h1>
            </div>
            <div class="menu">
                <button onclick="location.href='add_party.php'">Add New Participant</button>
                <button onclick="location.href='remove_party.php'">Remove Participant</button>
            </div>
        </div>
        <div id="right">
            <h1>All Standing Voting Parties</h1>
            <div id="header">
                <h3>Party Name</h3>
                <h3>Leader</h3>
                <h3>Image</h3>
                <h3 id="votes-header">Votes</h3>
                <h3>Customise</h3>
            </div>
            <div id="parties">
    <?php 
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="party">';
            echo '<p class="party-name">' . htmlspecialchars($row["party"]) . '</p>';
            echo '<p class="leader-name">' . htmlspecialchars($row["leader"]) . '</p>';
            echo '<img src="' . htmlspecialchars($row["image"]) . '" alt="Party Leader Image" class="party-image">';
            echo '<div class="votes">' . $row["votes"] . '</div>';
            // Form with hidden ID
            echo '<form action="../edit/index.php" method="get">';
            echo '<input type="hidden" name="id" value="' . htmlspecialchars($row["id"]) . '">';
            echo '<button type="submit">Edit</button>';
            echo '</form>';

            echo '</div>';
        }
    } else {
        echo '<div class="no-parties">No parties found in the database.</div>';
    }
    ?>
</div>

        </div>
    </div>


</body>

</html>

<?php
$conn->close();
?>