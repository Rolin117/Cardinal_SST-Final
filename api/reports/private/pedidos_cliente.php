<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;
// Se verifica si existe un valor para el cliente, de lo contrario se muestra un mensaje.
if (isset($_GET['id_cliente'])) {
    // Se incluyen las clases para la transferencia y acceso a datos.
    require_once('../../models/data/clientes_data.php');
    require_once('../../models/data/pedidos_data.php');
    // Se instancian las entidades correspondientes.
    $cliente = new ClientesData;
    $pedido = new PedidoData;
    // Se establece el valor del cliente, de lo contrario se muestra un mensaje.
    if ($cliente->setId($_GET['id_cliente']) && $pedido->setIdCliente($_GET['id_cliente'])) {
        // Se verifica si el cliente existe, de lo contrario se muestra un mensaje.
        if ($rowCliente = $cliente->readOne()) {
            // Se inicia el reporte con el encabezado del documento.
            $pdf->startReport('Pedidos realizados por ' . $rowCliente['nombre_cliente'] . ' ' . $rowCliente['apellido_cliente']);
            // Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
            if ($dataPedidos = $pedido->pedidosPorCliente()) {
                // Se establece un color de relleno para los encabezados.
                $pdf->setFillColor(50, 50, 50);
                // Se establece la fuente para los encabezados.
                $pdf->setFont('Arial', 'B', 11);
                $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados
                // Se imprimen las celdas con los encabezados.
                $pdf->cell(30, 10, 'ID Pedido', 1, 0, 'C', 1);
                $pdf->cell(30, 10, 'Fecha', 1, 0, 'C', 1);
                $pdf->cell(30, 10, 'Total (US$)', 1, 0, 'C', 1);
                $pdf->cell(30, 10, 'Cantidad', 1, 0, 'C', 1);
                $pdf->cell(70, 10, 'Producto', 1, 1, 'C', 1);

                // Se establece la fuente para los datos de los pedidos.
                $pdf->setFont('Arial', '', 11);
                $pdf->setTextColor(0, 0, 0); // Color de texto negro
                
                // Se recorren los registros fila por fila.
                $fill = false; // Alternancia de color de relleno
                foreach ($dataPedidos as $rowPedido) {
                    // Se imprimen las celdas con los datos de los pedidos.
                    $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
                    $pdf->cell(30, 10, $rowPedido['id_pedido'], 1, 0, 'C', $fill);
                    $pdf->cell(30, 10, $rowPedido['fecha'], 1, 0, 'C', $fill);
                    $pdf->cell(30, 10, $rowPedido['total'], 1, 0, 'R', $fill);
                    $pdf->cell(30, 10, $rowPedido['cantidad'], 1, 0, 'R', $fill);
                    $pdf->cell(70, 10, $pdf->encodeString($rowPedido['nombre_producto']), 1, 1, '', $fill);
                    // Alternar color de relleno
                    $fill = !$fill;
                }
            } else {
                $pdf->cell(0, 10, $pdf->encodeString('No hay pedidos para este cliente'), 1, 1);
            }
            // Se llama implícitamente al método footer() y se envía el documento al navegador web.
            $pdf->output('I', 'pedidos_cliente.pdf');
        } else {
            print('Cliente inexistente');
        }
    } else {
        print('Cliente incorrecto');
    }
} else {
    print('Debe seleccionar un cliente');
}
?>
