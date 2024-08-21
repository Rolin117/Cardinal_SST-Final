<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/oferta_data.php');

// Se instancian las entidades correspondientes.
$ofertaHandler = new OfertaHandler;

// Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
if ($dataOfertasProductos = $ofertaHandler->OfertasProductos()) {
    if (is_array($dataOfertasProductos) && count($dataOfertasProductos) > 0) {
        // Se inicia el reporte con el encabezado del documento.
        $pdf->startReport('Productos con Ofertas');

        // Se establece un color de relleno para los encabezados.
        $pdf->setFillColor(200, 200, 200);
        // Se establece la fuente para los encabezados.
        $pdf->setFont('Arial', 'B', 11);
        $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados

        // Se imprimen las celdas con los encabezados.
        $pdf->cell(60, 10, 'Nombre del Producto', 1, 0, 'C', 1);
        $pdf->cell(40, 10, 'Precio (US$)', 1, 0, 'C', 1);
        $pdf->cell(60, 10, 'Nombre de la Oferta', 1, 0, 'C', 1);
        $pdf->cell(30, 10, 'Descuento (%)', 1, 1, 'C', 1);

        // Se establece la fuente para los datos de los productos.
        $pdf->setFont('Arial', '', 11);
        $pdf->setTextColor(0, 0, 0); // Color de texto negro

        // Se recorren los registros fila por fila.
        $fill = false; // Alternancia de color de relleno
        foreach ($dataOfertasProductos as $rowOfertaProducto) {
            // Se imprimen las celdas con los datos de los productos y ofertas.
            $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
            $pdf->cell(60, 10, $pdf->encodeString($rowOfertaProducto['nombre_producto']), 1, 0, '', $fill);
            $pdf->cell(40, 10, '$' . number_format($rowOfertaProducto['precio_producto'], 2), 1, 0, 'R', $fill);
            $pdf->cell(60, 10, $pdf->encodeString($rowOfertaProducto['nombre_oferta']), 1, 0, '', $fill);
            $pdf->cell(30, 10, $rowOfertaProducto['descuento'] . '%', 1, 1, 'R', $fill);
            // Alternar color de relleno
            $fill = !$fill;
        }
    } else {
        $pdf->cell(0, 10, $pdf->encodeString('No hay productos con ofertas'), 1, 1, 'C');
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'productos_con_ofertas.pdf');
} else {
    print('Error al ejecutar la consulta');
}
?>
