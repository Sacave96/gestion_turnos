<?php
$servername = "localhost";
$username = "raspberry";
$password = "Admin1.msql";
$dbname = "sistema_turnos";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $seccion = $_POST['seccion'];
    if ($_POST['accion'] === 'siguiente') {
        $sql = "UPDATE turnos SET estado='atendido' WHERE estado='en espera' AND seccion='$seccion' ORDER BY id ASC LIMIT 1";
    } elseif ($_POST['accion'] === 'reiniciar') {
        $sql = "UPDATE turnos SET estado='en espera' WHERE estado != 'en espera' AND seccion='$seccion'";
    } elseif ($_POST['accion'] === 'anterior') {
        $sql = "UPDATE turnos SET estado='en espera' WHERE estado='atendido' AND seccion='$seccion' ORDER BY id DESC LIMIT 1";
    }
    $conn->query($sql);
    header("Location: trabajador.php");
    exit();
}

$sql = "SELECT numero_turno, seccion FROM turnos WHERE estado='en espera' ORDER BY id ASC";
$result = $conn->query($sql);
$turnos = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $turnos[$row['seccion']] = $row['numero_turno'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Turnos</title>
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
            font-size: 2em;
            margin: 20px 0;
        }
        button {
            font-size: 1em;
            padding: 10px 20px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Control de Turnos</h1>
        <?php foreach (['Carnicería', 'Pescadería', 'Frutería', 'Panadería'] as $seccion): ?>
            <p class="turno">Turno Actual (<?php echo $seccion; ?>): <?php echo $turnos[$seccion] ?? 'No hay turnos'; ?></p>
            <form method="post">
                <input type="hidden" name="seccion" value="<?php echo $seccion; ?>">
                <button type="submit" name="accion" value="siguiente">Siguiente Turno</button>
                <button type="submit" name="accion" value="anterior">Turno Anterior</button>
                <button type="submit" name="accion" value="reiniciar">Reiniciar Turnos</button>
            </form>
        <?php endforeach; ?>
    </div>
</body>
</html>



