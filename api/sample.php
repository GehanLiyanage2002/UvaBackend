<?php
// Sample API endpoint
require_once __DIR__ . '/../config/Cors.php';
header("Content-Type: application/json");

// Simulated database data
$data = [
    ["id" => 1, "name" => "John Doe", "email" => "john@example.com"],
    ["id" => 2, "name" => "Jane Smith", "email" => "jane@example.com"],
];

// Respond with the data
echo json_encode($data);
