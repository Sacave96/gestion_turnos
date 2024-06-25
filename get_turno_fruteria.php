<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "raspberry";
$password = "Admin1.msql";
$dbname = "sistema_turnos";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT numero_turno FROM turnos WHERE estado='en espera' AND seccion='Frutería' ORDER BY id ASC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['turno' => $row['numero_turno']]);
} else {
    echo json_encode(['turno' => 'No hay turnos']);
}

$conn->close();
?>
