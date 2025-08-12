<?php
include 'config.php';
session_start();

$data = json_decode(file_get_contents("php://input"), true);

if(!$data) {
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;
if(!$user_id){
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$name = mysqli_real_escape_string($conn, $data['name']);
$number = mysqli_real_escape_string($conn, $data['number']);
$email = mysqli_real_escape_string($conn, $data['email']);
$method = mysqli_real_escape_string($conn, $data['method']);
$address = mysqli_real_escape_string($conn, 'flat no. '. $data['address']['flat'].', '. $data['address']['street'].', '. $data['address']['city'].', '. $data['address']['country'].' - '. $data['address']['pin']);
$placed_on = date('d-M-Y');
$total_price = mysqli_real_escape_string($conn, $data['total']);

// Get cart items
$cart_products = [];
$cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
while($cart_item = mysqli_fetch_assoc($cart_query)){
    $cart_products[] = $cart_item['name'].' ('.$cart_item['quantity'].') ';
}
$total_products = implode(', ', $cart_products);

// Insert into orders table
mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$total_price', '$placed_on')");

// Clear the cart
mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'");

echo json_encode(["status" => "success"]);
?>










