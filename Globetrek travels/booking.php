<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "globetrek";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

/* Get booking details */
$package = $_POST["package"] ?? "";
$travel_date = $_POST["travel_date"] ?? "";
$travelers = $_POST["travelers"] ?? 1;

$first_name = $_POST["first_name"] ?? "";
$last_name = $_POST["last_name"] ?? "";
$email = $_POST["email"] ?? "";
$phone = $_POST["phone"] ?? "";

$accommodation = isset($_POST["accommodation"]) ? 1 : 0;
$transport = isset($_POST["transport"]) ? 1 : 0;

$special_request = $_POST["special_request"] ?? "";


/* Generate booking reference */
$booking_reference = "GT" . random_int(100000, 999999);


/* Package prices in LKR */
$packagePrices = [
    "Ella Adventure" => 37900,
    "Cultural Sri Lanka" => 49900,
    "Wild Escape" => 39900,
    "Southern Beach Escape" => 44900
];


/* Check package */
if (!isset($packagePrices[$package])) {
    die("Invalid package selected.");
}


/* Calculate prices */
$package_price = $packagePrices[$package];

$accommodation_price = 10000;
$transport_price = 7500;

$extra_price = 0;

if ($accommodation) {
    $extra_price += $accommodation_price;
}

if ($transport) {
    $extra_price += $transport_price;
}

$package_total = $package_price * $travelers;
$extra_total = $extra_price * $travelers;

$total_price = $package_total + $extra_total;


/* Insert into database */
$sql = "INSERT INTO bookings
(
    booking_reference,
    package,
    travel_date,
    travelers,
    first_name,
    last_name,
    email,
    phone,
    accommodation,
    transport,
    special_request,
    package_price,
    extra_price,
    total_price,
    booking_status
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}


$status = "Pending";


$stmt->bind_param(
    "sssisssiisdddss",
    $booking_reference,
    $package,
    $travel_date,
    $travelers,
    $first_name,
    $last_name,
    $email,
    $phone,
    $accommodation,
    $transport,
    $special_request,
    $package_total,
    $extra_total,
    $total_price,
    $status
);


/* Save booking */
if ($stmt->execute()) {

     echo "Booking successful! Your booking reference is: " . $booking_reference;
    

} else {

    echo "Booking failed: " . $stmt->error;
}


$stmt->close();
$conn->close();

?>