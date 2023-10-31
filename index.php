<?php
require_once __DIR__ . "/config/config.php";

// Obtiene la solicitud HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Obtiene la ruta de la solicitud
$request_uri = $_SERVER['REQUEST_URI'];

// Obtiene el endpoint
$endpoint = str_replace("/index.php/", "", $request_uri);

// Manejo de las solicitudes según el método y el endpoint
if ($method === 'GET' && $endpoint === 'patients') { /// patients ///
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
    sendOutput($message, ["$id"]);
} elseif ($method === 'PUT' && preg_match('/^patients\/(\d+)$/', $endpoint, $matches)) {
    $idPatient = $matches[1];
    //obtiene los datos del body
    parse_str(file_get_contents('php://input'), $_PUT);
    //actualizar paciente
    $name = $_PUT["name"];
    $surnames = $_PUT["surnames"];
    $birthdate = $_PUT["birthdate"];
    $phone = $_PUT["phone"];
    $photo = null;

    $sql = "UPDATE patients SET name = '$name', surnames = '$surnames', birthdate = '$birthdate', phone = '$phone', photourl = '$photo' WHERE idPatient = '$idPatient'";

    if (mysqli_query($conn, $sql)) {
        sendOutput("Actualizado con éxito.", ["1"]);
    } else {
        sendOutput("Error al actualizar.", ["-1"]);
    }
} elseif ($method === 'GET' && str_contains($endpoint, "queries")) { /// queries ///
    $get = $_GET["get"];
    // obtener todas las consultas
    $sql = "SELECT * FROM queries";
    $dateTime = $_GET["date"] ?? date('Y-m-d H:i:s');
    switch ($get) {
        case "day":
        case "today":
            $sql = "SELECT * FROM queries WHERE DATE(datetime) = DATE('$dateTime')";
            break;
        default: //all
            $sql = "SELECT * FROM queries";
            break;
    }
    $result = mysqli_query($conn, $sql);
    //responder con los datos
    sendOutput("Consultas obtenidas.", mysqli_fetch_all($result));
} elseif ($method === 'POST' && $endpoint === 'queries') {
    $dateTimeNow = date('Y-m-d H:i:s');
    $idPatient = intval($_POST["idPatient"]);
    $weight = doubleval($_POST["weight"]);
    $pressure = $_POST["pressure"];
    $temperature = doubleval($_POST["temperature"]);
    $currentsurgery = boolval($_POST["currentsurgery"]);
    $selfmedication = $_POST["selfmedication"];
    $diseasesandallergies = $_POST["diseasesandallergies"];
    $status = 1; // 1 = espera \\ 2 = abandono \\ 3 = atendido //

    // Comprueba si ya existe un registro con las mismas condiciones.
    $checkSql = "SELECT idQueries FROM queries WHERE patients_idPatient = '$idPatient' AND status = 1 AND prescription_idprescription IS NULL AND DATE(datetime) = DATE('$dateTimeNow')";
    $result = mysqli_query($conn, $checkSql);
    if (mysqli_num_rows($result) > 0) sendOutput("Ya existe un registro", ["-1"]);

    // Sentencia SQL para la inserción
    $sql = "INSERT INTO queries (datetime, weight, pressure, temperature, currentsurgery, selfmedication, diseasesandallergies, status, patients_idPatient) VALUES ('$dateTimeNow', '$weight', '$pressure', '$temperature', '$currentsurgery', '$selfmedication', '$diseasesandallergies', '$status', '$idPatient')";

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
    sendOutput($message, ["$id", "$dateTimeNow"]);
}