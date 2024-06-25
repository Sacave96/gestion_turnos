<?php
header('Content-Type: application/json');

function getTurnoActual() {
    $servername = "localhost";
    $username = "raspberry";
    $password = "Admin1.msql";
    $dbname = "sistema_turnos";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = "SELECT numero_turno FROM turnos WHERE estado='en espera' ORDER BY id ASC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['numero_turno'];
    } else {
        return "No hay turnos";
    }

    $conn->close();
}

$turno_actual = getTurnoActual();
echo json_encode(['turno' => $turno_actual]);
?>
