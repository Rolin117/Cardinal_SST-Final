<?php
// Encabezado para permitir solicitudes de cualquier origen.
header('Access-Control-Allow-Origin: *');
// Se establece la zona horaria local para la fecha y hora del servidor.
date_default_timezone_set('America/El_Salvador');
// Constantes para establecer las credenciales de conexión con el servidor de bases de datos.
define('SERVER', 'localhost');
define('DATABASE', 'db_cardinal');
define('USERNAME', 'Kevin'); 
define('PASSWORD', 'kero');

 // Intentar establecer conexión con la base de datos
//    $conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

//    if ($conn->connect_error) {
//       die("Conexión fallida: " . $conn->connect_error);
//    } else {
//       echo "¡Conexión exitosa!";
//    }

//   $conn->close();
