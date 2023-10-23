<?php
require_once __DIR__ . "/config/config.php";

// Obtiene la solicitud HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Obtiene la ruta de la solicitud
$request_uri = $_SERVER['REQUEST_URI'];

// Obtiene el endpoint
$endpoint = str_replace("/index.php/", "", $request_uri);

// Manejo de las solicitudes según el método y el endpoint
if ($method === 'GET' && $endpoint === 'patients') {
    // obtener todos los pacientes
    $sql = "SELECT * FROM patients";
    $result = mysqli_query($conn, $sql);
    //responder con los datos
    sendOutput("Pacientes obtenidos.", mysqli_fetch_all($result));
} elseif ($method === 'POST' && $endpoint === 'patients') {
    // insertar paciente
    $name = $_POST["name"];
    $surnames = $_POST["surnames"];
    $birthdate = $_POST["birthdate"];
    $phone = $_POST["phone"];
    $photo = null;

    // Sentencia SQL para la inserción
    $sql = "INSERT INTO patients (name, surnames, birthdate, phone, photourl) VALUES ('$name', '$surnames', '$birthdate', '$phone', '$photo')";

    //datos para la respuesta
    $message = "";
    $id = -1;

    // Ejecutar la consulta
    if (mysqli_query($conn, $sql)) {
        $id = mysqli_insert_id($conn);
        $message = "Insertado con éxito.";
    } else {
        $message = "Error al insertar.";
    }

    //responder con los datos
    sendOutput($message, ["idPatient" => $id]);
} elseif ($method === 'PUT' && preg_match('/^productos\/(\d+)$/', $endpoint, $matches)) {
    $idPatient = $matches[1];
    //actualizar paciente
}