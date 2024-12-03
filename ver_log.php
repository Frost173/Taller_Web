<?php
// Ruta al archivo de log
$logFile = 'error_log.txt';

// Verificar si el archivo existe
if (file_exists($logFile)) {
    // Leer el contenido del archivo y mostrarlo
    echo nl2br(file_get_contents($logFile));
} else {
    echo 'El archivo de log no existe.';
}
