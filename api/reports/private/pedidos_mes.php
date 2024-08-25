<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/pedidos_data.php');

// Se instancia la clase de datos de pedidos.
$pedido = new PedidoHandler; // Asume que tienes un handler para pedidos

// Se verifica si existen registros para mostrar.
if ($dataPedidos = $pedido->pedidosMes()) {
    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Pedidos por Mes');

    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(50, 50, 50);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Arial', 'B', 11);
    $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados

    // Se imprimen las celdas con los encabezados.
    $pdf->cell(30, 10, 'Mes', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Total Pedidos', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Ingresos Totales (US$)', 1, 1, 'C', 1);

    // Se establece la fuente para los datos de los pedidos.
    $pdf->setFont('Arial', '', 11);
    $pdf->setTextColor(0, 0, 0); // Color de texto negro

    // Se recorren los registros fila por fila.
    $fill = false; // Alternancia de color de relleno
    foreach ($dataPedidos as $rowPedido) {
        // Se imprimen las celdas con los datos de los pedidos.
        $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
        $pdf->cell(30, 10, $pdf->encodeString($rowPedido['mes']), 1, 0, '', $fill);
        $pdf->cell(40, 10, $rowPedido['total_pedidos'], 1, 0, 'R', $fill);
        $pdf->cell(40, 10, number_format($rowPedido['ingresos_totales'], 2), 1, 1, 'R', $fill);
        // Alternar color de relleno
        $fill = !$fill;
    }
} else {
    $pdf->cell(0, 10, $pdf->encodeString('No hay pedidos para mostrar'), 1, 1);
}

// Se llama implícitamente al método footer() y se envía el documento al navegador web.
$pdf->output('I', 'pedidos_mes.pdf');
?>
