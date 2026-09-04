<?php

// Connect to database

$host = "localhost";
$username = "root";
$password = "";
$database = "globetrek";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);

}


// Make sure the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}


// Get values from your HTML form
$firstName = trim($_POST["firstName"] ?? "");
$lastName = trim($_POST["lastName"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$country = trim($_POST["country"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";


// Check required fields
if (
    empty($firstName) ||
    empty($lastName) ||
    empty($email) ||
    empty($phone) ||
    empty($country) ||
    empty($password) ||
    empty($confirmPassword)
) {
    die("Please fill in all required fields.");
}


// Check email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}


// Check password confirmation
if ($password !== $confirmPassword) {
    die("Passwords do not match.");
}


// Check whether email already exists
$checkSql = "SELECT email FROM users WHERE email = ?";

$checkStmt = $conn->prepare($checkSql);

if (!$checkStmt) {
    die("Database error: " . $conn->error);
}

$checkStmt->bind_param("s", $email);

$checkStmt->execute();

$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    $checkStmt->close();
    $conn->close();

    die("This email address is already registered.");
}

$checkStmt->close();


// Hash the password
$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// Insert user into database
$sql = "INSERT INTO users
        (first_name, last_name, email, password, country, phone)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}


// Match the six values with the six database columns
$stmt->bind_param(
    "ssssss",
    $firstName,
    $lastName,
    $email,
    $hashedPassword,
    $country,
    $phone
);


// Execute INSERT
if ($stmt->execute()) {

    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Registration Successful</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                padding-top: 100px;
            }

            .success {
                color: green;
                font-size: 24px;
                margin-bottom: 20px;
            }

            a {
                text-decoration: none;
                background: #f1b50f;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
            }
        </style>
    </head>

    <body>

        <div class='success'>
            Registration successful!
        </div>

        <p>Your GlobeTrek account has been created.</p>

        <a href= 'login.html'>Sign In</a>

    </body>
    </html>
    ";

} else {

    echo "Registration failed: " . $stmt->error;
}


// Close connections
$stmt->close();
$conn->close();

?>