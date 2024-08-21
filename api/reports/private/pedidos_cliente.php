<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/cliente_data.php');
require_once('../../models/data/pedido_data.php');

// Se instancian las entidades correspondientes.
$cliente = new ClientesData;
$pedido = new PedidoData;

// Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
if ($dataPedidos = $pedido->pedidosClientes()) {
    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Pedidos por Cliente');

    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(50, 50, 50);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Arial', 'B', 11);
    $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados

    // Se imprimen las celdas con los encabezados.
    $pdf->cell(60, 10, 'Nombre del Cliente', 1, 0, 'C', 1);
    $pdf->cell(60, 10, 'Apellido del Cliente', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Fecha del Pedido', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Total (US$)', 1, 1, 'C', 1);

    // Se establece la fuente para los datos de los pedidos.
    $pdf->setFont('Arial', '', 11);
    $pdf->setTextColor(0, 0, 0); // Color de texto negro

    // Se recorren los registros fila por fila.
    $fill = false; // Alternancia de color de relleno
    foreach ($dataPedidos as $rowPedido) {
        // Se imprimen las celdas con los datos de los pedidos.
        $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
        $pdf->cell(60, 10, $pdf->encodeString($rowPedido['nombre_cliente']), 1, 0, '', $fill);
        $pdf->cell(60, 10, $pdf->encodeString($rowPedido['apellido_cliente']), 1, 0, '', $fill);
        $pdf->cell(40, 10, $rowPedido['fecha_pedido'], 1, 0, 'C', $fill);
        $pdf->cell(30, 10, number_format($rowPedido['total_pedido'], 2), 1, 1, 'R', $fill);
        // Alternar color de relleno
        $fill = !$fill;
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'pedidos_cliente.pdf');
} else {
    print('No hay pedidos para mostrar');
}
?>
