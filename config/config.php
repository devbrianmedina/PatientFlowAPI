<?php
// Establecer encabezados
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

const URL_BASE = "http://192.168.100.4/index.php/";

/**
 * @param string $message
 * @param array $data
 * @return void
 */
function sendOutput(string $message, array $data = []): void {
    echo json_encode(
        [
            "message" => $message,
            "data" => $data
        ]
    );
    exit;
}


$servername = "localhost";
$username = "brian";
$password = "237815";
$dbname = "patientflow";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    sendOutput("Error interno.");
}