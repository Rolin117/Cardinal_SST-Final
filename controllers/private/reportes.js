const openReportA = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/productos_agotados.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

const openReportS = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/productos_stock.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

const openReportO = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/productos_oferta.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}


const openReportD = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/productos_demanda.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

const openReportM = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/ingresos_mes.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

const openReportVM = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/ingresos_mesP.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

const openReportPM = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/pedidos_mes.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

const openReportPA = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/admin_productos.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

const openReportSA = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/admin_servicios.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}