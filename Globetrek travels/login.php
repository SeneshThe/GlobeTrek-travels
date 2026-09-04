
<?php

session_start();

// Database connection
$host = "localhost";
$username = "root";
$dbPassword = "";
$database = "globetrek";

$conn = new mysqli($host, $username, $dbPassword, $database);

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


// Check if form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}


// Get login details
$email = trim($_POST["email"] ?? "");
$userPassword = $_POST["password"] ?? "";


// Check empty fields
if (empty($email) || empty($userPassword)) {
    die("Please enter your email and password.");
}


// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}


// Find user by email
$sql = "SELECT first_name, last_name, email, password, country, phone, role
        FROM users
        WHERE email = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();


// Check if account exists
if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($userPassword, $user["password"])) {

        // Store user information in session
        $_SESSION["email"] = $user["email"];
        $_SESSION["first_name"] = $user["first_name"];
        $_SESSION["last_name"] = $user["last_name"];
        $_SESSION["country"] = $user["country"];
        $_SESSION["phone"] = $user["phone"];
        $_SESSION["role"] = $user["role"];


        // Login successful
       
    if ($user["role"] === "admin") {

    echo "<script>
        window.location.href = 'admindashboard.html';
    </script>";

} elseif ($user["role"] === "staff") {

    echo "<script>
        window.location.href = 'staffdashboard.html';
    </script>";

} elseif ($user["role"] === "customer") {

    echo "<script>
        window.location.href = 'customerdashboard.html';
    </script>";

} else {

    die("Invalid account role.");

}

exit();


  } 

} 


// Close connection
$stmt->close();
$conn->close();

?>

