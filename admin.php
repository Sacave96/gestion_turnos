<?php
$servername = "localhost";
$username = "raspberry";
$password = "Admin1.msql";
$dbname = "sistema_turnos";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['exportar'])) {
    $sql = "SELECT * FROM turnos";
    $result = $conn->query($sql);

    $filename = "turnos_" . date('Ymd') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);

    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Número de Turno', 'ID de Cliente', 'Estado', 'Sección', 'Timestamp'));

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $id = $_POST['id'];
    $numero_turno = $_POST['numero_turno'];
    $cliente_id = $_POST['cliente_id'];
    $estado = $_POST['estado'];
    $seccion = $_POST['seccion'];

    if ($_POST['accion'] === 'modificar') {
        $sql = "UPDATE turnos SET numero_turno='$numero_turno', cliente_id='$cliente_id', estado='$estado', seccion='$seccion' WHERE id='$id'";
    } elseif ($_POST['accion'] === 'borrar') {
        $sql = "DELETE FROM turnos WHERE id='$id'";
    } elseif ($_POST['accion'] === 'agregar') {
        $sql = "INSERT INTO turnos (numero_turno, cliente_id, estado, seccion) VALUES ('$numero_turno', '$cliente_id', '$estado', '$seccion')";
    }
    $conn->query($sql);
    header("Location: admin.php");
    exit();
}

$sql = "SELECT * FROM turnos";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Turnos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
            width: 300px;
            margin-bottom: 20px;
        }
        .form-container form input, .form-container form select {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #dddddd;
        }
        .form-container form button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Administración de Turnos</h1>

    <div class="form-container">
        <form method="post">
            <input type="hidden" name="accion" value="agregar">
            <input type="text" name="numero_turno" placeholder="Número de Turno" required>
            <input type="text" name="cliente_id" placeholder="ID de Cliente" required>
            <select name="estado" required>
                <option value="en espera">En espera</option>
                <option value="atendido">Atendido</option>
                <option value="cancelado">Cancelado</option>
            </select>
            <select name="seccion" required>
                <option value="Carnicería">Carnicería</option>
                <option value="Pescadería">Pescadería</option>
                <option value="Frutería">Frutería</option>
                <option value="Panadería">Panadería</option>
            </select>
            <button type="submit">Agregar Turno</button>
        </form>

        <form method="post">
            <button type="submit" name="exportar" value="exportar">Exportar Datos</button>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Número de Turno</th>
            <th>ID de Cliente</th>
            <th>Estado</th>
            <th>Sección</th>
            <th>Timestamp</th>
            <th>Acciones</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <form method="post">
                        <td><?php echo $row['id']; ?></td>
                        <td><input type="text" name="numero_turno" value="<?php echo $row['numero_turno']; ?>"></td>
                        <td><input type="text" name="cliente_id" value="<?php echo $row['cliente_id']; ?>"></td>
                        <td>
                            <select name="estado">
                                <option value="en espera" <?php if($row['estado'] == 'en espera') echo 'selected'; ?>>En espera</option>
                                <option value="atendido" <?php if($row['estado'] == 'atendido') echo 'selected'; ?>>Atendido</option>
                                <option value="cancelado" <?php if($row['estado'] == 'cancelado') echo 'selected'; ?>>Cancelado</option>
                            </select>
                        </td>
                        <td>
                            <select name="seccion">
                                <option value="Carnicería" <?php if($row['seccion'] == 'Carnicería') echo 'selected'; ?>>Carnicería</option>
                                <option value="Pescadería" <?php if($row['seccion'] == 'Pescadería') echo 'selected'; ?>>Pescadería</option>
                                <option value="Frutería" <?php if($row['seccion'] == 'Frutería') echo 'selected'; ?>>Frutería</option>
                                <option value="Panadería" <?php if($row['seccion'] == 'Panadería') echo 'selected'; ?>>Panadería</option>
                            </select>
                        </td>
                        <td><?php echo $row['timestamp']; ?></td>
                        <td>
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="accion" value="modificar">Modificar</button>
                            <button type="submit" name="accion" value="borrar">Borrar</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>
</body>
</html>

