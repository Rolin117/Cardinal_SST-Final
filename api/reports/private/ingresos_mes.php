<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/pedidos_data.php');

// Se instancian las entidades correspondientes.
$pedido = new PedidoHandler;

// Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
if ($dataIngresosMes = $pedido->IngresosMes()) {
    if (is_array($dataIngresosMes) && count($dataIngresosMes) > 0) {
        // Se inicia el reporte con el encabezado del documento.
        $pdf->startReport('Ingresos por Mes');

        // Se establece un color de relleno para los encabezados.
        $pdf->setFillColor(50, 50, 50);
        // Se establece la fuente para los encabezados.
        $pdf->setFont('Arial', 'B', 11);
        $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados

        // Se imprimen las celdas con los encabezados.
        $pdf->cell(80, 10, 'Mes', 1, 0, 'C', 1);
        $pdf->cell(60, 10, 'Ingresos Totales', 1, 1, 'C', 1);

        // Se establece la fuente para los datos.
        $pdf->setFont('Arial', '', 11);
        $pdf->setTextColor(0, 0, 0); // Color de texto negro

        // Se recorren los registros fila por fila.
        $fill = false; // Alternancia de color de relleno
        foreach ($dataIngresosMes as $rowIngreso) {
            // Se imprimen las celdas con los datos de los ingresos por mes.
            $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
            $pdf->cell(80, 10, $pdf->encodeString($rowIngreso['mes']), 1, 0, '', $fill);
            $pdf->cell(60, 10, '$' . number_format($rowIngreso['ingresos_totales'], 2), 1, 1, 'R', $fill);
            // Alternar color de relleno
            $fill = !$fill;
        }

        // Se llama implícitamente al método footer() y se envía+ el documento al navegador web.
        $pdf->output('I', 'ingresos_por_mes.pdf');
    } else {
        print('No hay ingresos para mostrar');
    }
} else {
    print('Error al ejecutar la consulta');
}
?>
