

const CATEGORIA_API = 'services/private/categorias.php';
const PRODUCTO_API = 'services/private/productos.php';
const OFERTA_API = 'services/private/ofertas.php';
const VENTA_API = 'services/private/ventas.php';
const SERVICIOS_API = 'services/private/servicios.php';
const PEDIDO_API = 'services/private/pedidos.php';


document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    // Llamada a las funciones que generan los gráficos en la página web.
    getCategoriaAdvancedStats();
    OfertasProductosA();
    getVentasTotalesPorFecha();
    graficoServicios();
    PrecioCategorias();
    productosCategorias();
    ventasPorProducto();
    pedidosPorCliente();
    ventasMensuales();
    inventarioProductos();

});

const getCategoriaAdvancedStats = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(CATEGORIA_API, 'getCategoriaAdvancedStats');
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let categorias = [];
            let totalProductos = [];
            let precioMinimo = [];
            let precioMaximo = [];
            let rangoPrecios = [];
            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                categorias.push(row.nombre_cat);
                totalProductos.push(row.total_productos);
                precioMinimo.push(row.precio_minimo);
                precioMaximo.push(row.precio_maximo);
                rangoPrecios.push(row.rango_precios);
            });
            // Llamada a la función para generar y mostrar un gráfico de barras.
            createBarChart('chart1', categorias, totalProductos, precioMinimo, precioMaximo, rangoPrecios, 'Estadísticas Avanzadas de Categorías');

        } else {
            document.getElementById('chart1').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching advanced category stats:', error);
    }
}

const createBarChart = (id, categorias, totalProductos, precioMinimo, precioMaximo, rangoPrecios, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categorias,
            datasets: [
                {
                    label: 'Total Productos',
                    data: totalProductos,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Precio Mínimo',
                    data: precioMinimo,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Precio Máximo',
                    data: precioMaximo,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Rango Precios',
                    data: rangoPrecios,
                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
                    borderColor: 'rgba(255, 205, 86, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: title
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}

const OfertasProductosA = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(OFERTA_API, 'OfertasProductosA');
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let productos = [];
            let totalOfertas = [];

            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                productos.push(row.nombre_producto);
                totalOfertas.push(row.total_ofertas);
            });
            // Llamada a la función para generar y mostrar un gráfico de tarta.
            createPieChart('chart2', productos, totalOfertas, 'Estadísticas de Ofertas por Producto');

        } else {
            document.getElementById('chart2').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching offers per product stats:', error);
    }
}

const createPieChart = (id, labels, data, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'pie', // Cambio a gráfico de tarta
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(199, 199, 199, 0.2)',
                    'rgba(83, 102, 255, 0.2)',
                    'rgba(54, 162, 235, 0.4)',
                    'rgba(255, 159, 64, 0.4)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(199, 199, 199, 1)',
                    'rgba(83, 102, 255, 1)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                },
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            return `${tooltipItem.label}: ${tooltipItem.raw}`;
                        }
                    }
                }
            }
        }
    });
}







const getVentasTotalesPorFecha = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(VENTA_API, 'getVentasTotalesPorFecha');
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let fechas = [];
            let totalVentas = [];

            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                fechas.push(row.fecha_venta);
                totalVentas.push(row.total_ventas);
            });
            // Llamada a la función para generar y mostrar un gráfico de líneas.
            createLineChart('chart4', fechas, totalVentas, 'Ventas Totales por Fecha');

        } else {
            document.getElementById('chart4').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching total sales by date:', error);
    }
}

const createLineChart = (id, labels, data, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Ventas',
                data: data,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day', // Puedes ajustar la unidad según tus datos
                        tooltipFormat: 'P' // Formato de fecha para la visualización en el tooltip
                    },
                    title: {
                        display: true,
                        text: 'Fecha'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total Ventas'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}

const graficoServicios = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(SERVICIOS_API, 'graficoServicios');

        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let administradores = [];
            let totalServicios = [];

            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                administradores.push(row.administrador);
                totalServicios.push(row.total_servicios);
            });

            // Llamada a la función para generar y mostrar un gráfico de barras.
            createBarChart2('chart3', administradores, totalServicios, 'Total de Servicios por Administrador');

        } else {
            document.getElementById('chart3').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching services data:', error);
    }
}

const createBarChart2 = (id, labels, data, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Servicios',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Administradores'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total Servicios'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}




const PrecioCategorias = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(CATEGORIA_API, 'PrecioCategorias');

        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let categorias = [];
            let preciosPromedio = [];

            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                categorias.push(row.nombre_cat);
                preciosPromedio.push(row.precio_promedio);
            });

            // Llamada a la función para generar y mostrar un gráfico de barras.
            createBarChart3('chart5', categorias, preciosPromedio, 'Precio Promedio por Categoría');

        } else {
            document.getElementById('chart5').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching average price data:', error);
    }
}

const createBarChart3 = (id, labels, data, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Precio Promedio',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Categorías'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Precio Promedio'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}


const productosCategorias = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(CATEGORIA_API, 'productosCategorias');

        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let categorias = [];
            let totalProductos = [];

            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                categorias.push(row.nombre_cat);
                totalProductos.push(row.total_productos);
            });

            // Llamada a la función para generar y mostrar un gráfico de barras.
            createBarChart4('chart6', categorias, totalProductos, 'Total de Productos por Categoría');

        } else {
            document.getElementById('chart6').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching products by category data:', error);
    }
}

const createBarChart4 = (id, labels, data, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total de Productos',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Categorías'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total de Productos'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}

const ventasPorProducto = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(VENTA_API, 'ventasPorProducto');

        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let productos = [];
            let totalVendida = [];

            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                productos.push(row.nombre_producto);
                totalVendida.push(row.total_vendida);
            });

            // Llamada a la función para generar y mostrar un gráfico de barras.
            createBarChartVentas('chartVentas', productos, totalVendida, 'Total de Ventas por Producto');

        } else {
            document.getElementById('chartVentas').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching sales data by product:', error);
    }
}

const createBarChartVentas = (id, labels, data, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Vendida',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Productos'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total Vendida'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}


const pedidosPorCliente = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(PEDIDO_API, 'pedidosPorClienteG');

        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let clientes = [];
            let totalPedidos = [];

            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                clientes.push(row.cliente);
                totalPedidos.push(row.total_pedidos);
            });

            // Llamada a la función para generar y mostrar un gráfico de barras.
            createBarChartPedidos('chartPedidos', clientes, totalPedidos, 'Total de Pedidos por Cliente');

        } else {
            document.getElementById('chartPedidos').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching orders by client data:', error);
    }
}

const createBarChartPedidos = (id, labels, data, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total de Pedidos',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Clientes'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total de Pedidos'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}



const ventasMensuales = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(VENTA_API, 'VentasMensualesP');
        
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let meses = [];
            let totalVentas = [];
            
            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                meses.push(row.mes);
                totalVentas.push(row.total_ventas);
            });

            // Llamada a la función para generar y mostrar un gráfico de líneas.
            createLineChart2('chartVentasMensuales', meses, totalVentas, 'Ventas Mensuales');
            
        } else {
            document.getElementById('chartVentasMensuales').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching monthly sales data:', error);
    }
}

const createLineChart2 = (id, labels, data, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Ventas',
                data: data,
                fill: false, // No llenar el área bajo la línea
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1 // Suaviza la línea
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Meses'
                    },
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 12
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total Ventas'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}

const inventarioProductos = async () => {
    try {
        // Petición para obtener los datos del gráfico.
        const DATA = await fetchData(VENTA_API, 'InventarioProductosP');
        
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
        if (DATA.status) {
            // Se declaran los arreglos para guardar los datos a graficar.
            let productos = [];
            let totalVendidos = [];
            
            // Se recorre el conjunto de registros fila por fila a través del objeto row.
            DATA.dataset.forEach(row => {
                // Se agregan los datos a los arreglos.
                productos.push(row.nombre_producto);
                totalVendidos.push(row.total_vendido);
            });

            // Llamada a la función para generar y mostrar un gráfico de barras.
            createBarChartInventario('chartInventario', productos, totalVendidos, 'Top 10 Productos Más Vendidos');
            
        } else {
            document.getElementById('chartInventario').remove();
            console.log(DATA.error);
        }
    } catch (error) {
        console.error('Error fetching inventory products data:', error);
    }
}

const createBarChartInventario = (id, labels, data, title) => {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Vendido',
                data: data,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Productos'
                    },
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 10
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Total Vendido'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}

