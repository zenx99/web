<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "movie_reviews";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$movie_name = "Example Movie"; // เปลี่ยนชื่อภาพยนตร์ที่ต้องการรีวิว

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// รับคะแนนจากผู้ใช้
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $score = $_POST['score'];
    $sql = "SELECT * FROM reviews WHERE movie_name='$movie_name' AND user_id='$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "คุณได้รีวิวแล้ว";
    } else {
        $sql = "INSERT INTO reviews (movie_name, score, user_id) VALUES ('$movie_name', '$score', '$user_id')";
        if ($conn->query($sql) === TRUE) {
            echo "รีวิวถูกบันทึก!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// คำนวณคะแนน
$sql = "SELECT COUNT(*) AS total_reviews, SUM(CASE WHEN score = 1 THEN 1 ELSE 0 END) AS positive_reviews FROM reviews WHERE movie_name='$movie_name'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_reviews = $row['total_reviews'];
$positive_reviews = $row['positive_reviews'];
$negative_reviews = $total_reviews - $positive_reviews;

$positive_percentage = $total_reviews > 0 ? round(($positive_reviews / $total_reviews) * 100, 2) : 0;
$negative_percentage = $total_reviews > 0 ? round(($negative_reviews / $total_reviews) * 100, 2) : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เว็บรีวิวภาพยนตร์</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        body.dark-mode {
            background-color: #121212;
            color: #fff;
        }
        .container {
            text-align: center;
        }
        .button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
            border: none;
            border-radius: 5px;
        }
        .good {
            background-color: #4CAF50;
            color: white;
        }
        .bad {
            background-color: #f44336;
            color: white;
        }
        .toggle-theme {
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>รีวิวภาพยนตร์: <?php echo $movie_name; ?></h1>
        <form method="post">
            <button type="submit" name="score" value="1" class="button good">เยี่ยม</button>
            <button type="submit" name="score" value="-1" class="button bad">แย่</button>
        </form>

        <p>คะแนนปัจจุบัน:</p>
        <p>เยี่ยม: <?php echo $positive_percentage; ?>%</p>
        <p>แย่: <?php echo $negative_percentage; ?>%</p>

        <button class="toggle-theme" onclick="toggleTheme()">สลับธีม</button>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle("dark-mode");
        }
    </script>
</body>
</html>
