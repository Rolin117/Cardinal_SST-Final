<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/servicio_data.php');

// Se instancia la entidad correspondiente.
$servicio = new ServicioData;

// Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
if ($dataServicios = $servicio->serviciosAdmin()) {
    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Servicios por Administrador');

    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(50, 50, 50);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Arial', 'B', 11);
    $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados

    // Se imprimen las celdas con los encabezados.
    $pdf->cell(60, 10, 'Nombre Administrador', 1, 0, 'C', 1);
    $pdf->cell(60, 10, 'Apellido Administrador', 1, 0, 'C', 1);
    $pdf->cell(60, 10, 'Nombre del Servicio', 1, 1, 'C', 1);

    // Se establece la fuente para los datos de los servicios.
    $pdf->setFont('Arial', '', 11);
    $pdf->setTextColor(0, 0, 0); // Color de texto negro

    // Se recorren los registros fila por fila.
    $fill = false; // Alternancia de color de relleno
    foreach ($dataServicios as $rowServicio) {
        // Se imprimen las celdas con los datos de los servicios.
        $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
        $pdf->cell(60, 10, $pdf->encodeString($rowServicio['nombre_administrador']), 1, 0, '', $fill);
        $pdf->cell(60, 10, $pdf->encodeString($rowServicio['apellido_administrador']), 1, 0, '', $fill);
        $pdf->cell(60, 10, $pdf->encodeString($rowServicio['nombre_servicio']), 1, 1, 'R', $fill);
        // Alternar color de relleno
        $fill = !$fill;
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'servicios_admin.pdf');
} else {
    print('No hay servicios para mostrar');
}
?>
