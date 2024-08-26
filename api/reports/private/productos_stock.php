<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;
// Se verifica si existe un valor para la cantidad mínima de stock, de lo contrario se muestra un mensaje.
if (isset($_GET['cantidad_minima'])) {
    // Se incluye la clase para la transferencia y acceso a datos.
    require_once('../../models/data/productos_data.php');
    // Se instancia la entidad correspondiente.
    $producto = new ProductoData;
    // Se establece el valor de la cantidad mínima, de lo contrario se muestra un mensaje.
    if ($producto->setCantidad($_GET['cantidad_minima'])) {
        // Se inicia el reporte con el encabezado del documento.
        $pdf->startReport('Productos con stock igual o menor a ' . $_GET['cantidad_minima']);
        // Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
        if ($dataProductos = $producto->productosBajoStock()) {
            // Se establece un color de relleno para los encabezados.
            $pdf->setFillColor(50, 50, 50);
            // Se establece la fuente para los encabezados.
            $pdf->setFont('Arial', 'B', 11);
            $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados
            // Se imprimen las celdas con los encabezados.
            $pdf->cell(96, 10, 'Nombre', 1, 0, 'C', 1);
            $pdf->cell(30, 10, 'Cantidad', 1, 0, 'C', 1);
            $pdf->cell(30, 10, 'Precio (US$)', 1, 1, 'C', 1);

            // Se establece la fuente para los datos de los productos.
            $pdf->setFont('Arial', '', 11);
            $pdf->setTextColor(0, 0, 0); // Color de texto negro
            
            // Se recorren los registros fila por fila.
            $fill = false; // Alternancia de color de relleno
            foreach ($dataProductos as $rowProducto) {
                // Se imprimen las celdas con los datos de los productos.
                $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
                $pdf->cell(96, 10, $pdf->encodeString($rowProducto['nombre_producto']), 1, 0, '', $fill);
                $pdf->cell(30, 10, $rowProducto['cantidad_producto'], 1, 0, 'R', $fill);
                $pdf->cell(30, 10, $rowProducto['precio_producto'], 1, 1, 'R', $fill);
                // Alternar color de relleno
                $fill = !$fill;
            }
        } else {
            $pdf->cell(0, 10, $pdf->encodeString('No hay productos con stock igual o menor a ' . $_GET['cantidad_minima']), 1, 1);
        }
        // Se llama implícitamente al método footer() y se envía el documento al navegador web.
        $pdf->output('I', 'productos_bajo_stock.pdf');
    } else {
        print('Cantidad mínima incorrecta');
    }
} else {
    print('Debe proporcionar una cantidad mínima de stock');
}
?>
