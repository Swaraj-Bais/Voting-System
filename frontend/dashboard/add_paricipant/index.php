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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $party = $_POST['party'];
    $leader = $_POST['leader'];
    $image = $_POST['image'];

    // votes start with 0
    $votes = 0;

    $sql = "INSERT INTO parties (party, leader, votes, image) 
            VALUES ('$party', '$leader', $votes, '$image')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>New party added successfully!</p>";
        // Redirect to the dashboard or another page after successful submissio
        header("Location: ../view/index.php");
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
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
       <div class="left">
           <h1>New Party For Voting</h1>
       </div>
   <form method="POST">
    
       <label>Enter Party Name</label>
       <input type="text" name="party" placeholder="Enter party name" required />

       <label>Leader Name</label>
       <input type="text" name="leader" placeholder="Enter leader name" required />

       <label>Party Symbol (Image URL)</label>
       <input type="text" name="image" placeholder="Enter image URL" required />

       <button type="submit">Add Party</button>
   </form>
  
   </div>
</body>
</html>

<?php
$conn->close();
?>


