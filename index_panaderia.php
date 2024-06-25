<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "raspberry";
$password = "Admin1.msql";
$dbname = "sistema_turnos";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener el turno actual de Panadería
$sql = "SELECT numero_turno FROM turnos WHERE estado='en espera' AND seccion='Panadería' ORDER BY id ASC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $turno_actual = $row['numero_turno'];
} else {
    $turno_actual = "No hay turnos";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos Panadería</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .container {
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .turno {
            font-size: 3em;
            margin: 0;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function actualizarTurno() {
                $.ajax({
                    url: 'get_turno_panaderia.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('.turno').text(data.turno);
                    }
                });
            }
            setInterval(actualizarTurno, 5000); // Actualiza cada 5 segundos
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Turno Actual (Panadería)</h1>
        <p class="turno"><?php echo $turno_actual; ?></p>
    </div>
</body>
</html>
