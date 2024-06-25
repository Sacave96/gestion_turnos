<?php
$servername = "localhost";
$username = "raspberry";
$password = "Admin1.msql";
$dbname = "sistema_turnos";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM turnos";
$result = $conn->query($sql);

$turnos_por_dia = [];
$tiempos_espera = [];
$turnos_por_seccion = ['Carnicería' => 0, 'Pescadería' => 0, 'Frutería' => 0, 'Panadería' => 0];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $fecha = date('Y-m-d', strtotime($row['timestamp']));
        if (!isset($turnos_por_dia[$fecha])) {
            $turnos_por_dia[$fecha] = 0;
        }
        $turnos_por_dia[$fecha]++;
        $turnos_por_seccion[$row['seccion']]++;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos Estadísticos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px;
        }
        .chart-container {
            width: 400px;
            height: 300px;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-bottom: 20px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Gráficos Estadísticos</h1>
    <button onclick="window.location.href='admin.php'">Volver</button>

    <div class="chart-container">
        <canvas id="turnosPorDia"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="tiempoEspera"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="turnosPorSeccion"></canvas>
    </div>

    <script>
        const turnosPorDiaCtx = document.getElementById('turnosPorDia').getContext('2d');
        const tiempoEsperaCtx = document.getElementById('tiempoEspera').getContext('2d');
        const turnosPorSeccionCtx = document.getElementById('turnosPorSeccion').getContext('2d');

        const turnosPorDiaData = {
            labels: <?php echo json_encode(array_keys($turnos_por_dia)); ?>,
            datasets: [{
                label: 'Turnos Atendidos por Día',
                data: <?php echo json_encode(array_values($turnos_por_dia)); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        const tiempoEsperaData = {
            labels: <?php echo json_encode(array_keys($turnos_por_dia)); ?>,
            datasets: [{
                label: 'Tiempo Promedio de Espera (minutos)',
                data: <?php echo json_encode(array_values($turnos_por_dia)); ?>,
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        };

        const turnosPorSeccionData = {
            labels: <?php echo json_encode(array_keys($turnos_por_seccion)); ?>,
            datasets: [{
                label: 'Turnos por Sección',
                data: <?php echo json_encode(array_values($turnos_por_seccion)); ?>,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        };

        new Chart(turnosPorDiaCtx, {
            type: 'bar',
            data: turnosPorDiaData,
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(tiempoEsperaCtx, {
            type: 'line',
            data: tiempoEsperaData,
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(turnosPorSeccionCtx, {
            type: 'pie',
            data: turnosPorSeccionData,
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html>

