<?php
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

$led = $part = $img = "";
$id = "";

// Fetch party details if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM parties WHERE id = '$id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $led  = $row['leader'];
        $part = $row['party'];
        $img  = $row['image'];
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $party  = $_POST['party'];
    $leader = $_POST['leader'];
    $image  = $_POST['image'];
    $id     = $_POST['id'];

    // Update query instead of insert
    $sql = "UPDATE parties 
            SET party = '$party', leader = '$leader', image = '$image'
            WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Party updated successfully!</p>";
        header("Location: ../view/index.php");
        exit;
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Participant</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
   <div id="main">
       <div class="left">
           <h1>Edit the Participant</h1>
       </div>
       <form method="POST">
           <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" />

           <label>Enter Party Name</label>
           <input value="<?php echo htmlspecialchars($part); ?>" type="text" name="party" required />

           <label>Leader Name</label>
           <input value="<?php echo htmlspecialchars($led); ?>" type="text" name="leader" required />

           <label>Party Symbol (Image URL)</label>
           <input value="<?php echo htmlspecialchars($img); ?>" type="text" name="image" required />

           <button type="submit">Update Party</button>
       </form>
   </div>
</body>
</html>
