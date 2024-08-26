<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se incluye la clase para la transferencia y acceso a datos.
require_once('../../models/data/productos_data.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se instancian las entidades correspondientes.
$prediccion = new ProductoHandler;

// Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
if ($dataProductosDemanda = $prediccion->prediccionProductosDemanda()) {
    if (is_array($dataProductosDemanda) && count($dataProductosDemanda) > 0) {
        // Se inicia el reporte con el encabezado del documento.
        $pdf->startReport('Predicción de Productos en Demanda por Ofertas');

        // Se establece un color de relleno para los encabezados.
        $pdf->setFillColor(50, 50, 50);
        // Se establece la fuente para los encabezados.
        $pdf->setFont('Arial', 'B', 11);
        $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados

        // Se imprimen las celdas con los encabezados.
        $pdf->cell(60, 10, 'Producto', 1, 0, 'C', 1);
        $pdf->cell(40, 10, 'Precio', 1, 0, 'C', 1);
        $pdf->cell(50, 10, 'Oferta', 1, 0, 'C', 1);
        $pdf->cell(50, 10, 'Cantidad Vendida', 1, 1, 'C', 1);

        // Se establece la fuente para los datos.
        $pdf->setFont('Arial', '', 11);
        $pdf->setTextColor(0, 0, 0); // Color de texto negro

        // Se recorren los registros fila por fila.
        $fill = false; // Alternancia de color de relleno
        foreach ($dataProductosDemanda as $rowProducto) {
            // Se imprimen las celdas con los datos de los productos.
            $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
            $pdf->cell(60, 10, $pdf->encodeString($rowProducto['nombre_producto']), 1, 0, '', $fill);
            $pdf->cell(40, 10, '$' . number_format($rowProducto['precio_producto'], 2), 1, 0, 'R', $fill);
            $pdf->cell(50, 10, $pdf->encodeString($rowProducto['nombre_oferta']), 1, 0, '', $fill);
            $pdf->cell(50, 10, $rowProducto['cantidad_total_vendida'], 1, 1, 'R', $fill);
            // Alternar color de relleno
            $fill = !$fill;
        }

        // Se llama implícitamente al método footer() y se envía el documento al navegador web.
        $pdf->output('I', 'prediccion_productos_en_demanda.pdf');
    } else {
        print('No hay datos para mostrar');
    }
} else {
    print('Error al ejecutar la consulta');
}
?>
