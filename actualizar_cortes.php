<?php
require 'conexion.php';

// Consultar los cortes con apertura_id = 0
$sqlCortes = "SELECT id, creado_en FROM corte_caja WHERE apertura_id = 0 ORDER BY creado_en";
$resultCortes = $mysqli->query($sqlCortes);

if ($resultCortes->num_rows > 0) {
    while ($corte = $resultCortes->fetch_assoc()) {
        $corte_id = $corte['id'];
        $creado_en = $corte['creado_en'];

        // Buscar la apertura más cercana anterior a la fecha del corte
        $sqlApertura = "SELECT id FROM aperturas_caja WHERE fecha_apertura <= '$creado_en' ORDER BY fecha_apertura DESC LIMIT 1";
        $resultApertura = $mysqli->query($sqlApertura);

        if ($resultApertura->num_rows > 0) {
            $apertura = $resultApertura->fetch_assoc();
            $apertura_id = $apertura['id'];

            // Actualizar el corte con el apertura_id encontrado
            $sqlUpdate = "UPDATE corte_caja SET apertura_id = $apertura_id WHERE id = $corte_id";
            if ($mysqli->query($sqlUpdate) === TRUE) {
                echo "Corte ID: " . $corte_id . " actualizado con apertura_id: " . $apertura_id . "<br>";
            } else {
                echo "Error al actualizar el corte ID: " . $corte_id . ": " . $mysqli->error . "<br>";
            }
        } else {
            echo "No se encontró apertura para el corte ID: " . $corte_id . "<br>";
        }
    }
} else {
    echo "No se encontraron cortes con apertura_id = 0";
}

$mysqli->close();
?>
