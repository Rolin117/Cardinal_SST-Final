// Constantes para completar las rutas de la API.
const PRODUCTO_API = 'services/private/productos.php';
const CATEGORIA_API = 'services/private/categorias.php';

const SEARCH_FORM = document.getElementById('searchForm');

const TABLE_BODY = document.getElementById('tarjetas');

const SAVE_MODAL = new bootstrap.Modal(document.getElementById('saveModal')),
    MODAL_TITLE = document.getElementById('modalTitle');

const SAVE_FORM = document.getElementById('saveForm'),
    ID_PRODUCTO = document.getElementById('id_producto'),
    NOMBRE_PRODUCTO = document.getElementById('nombre_producto'),
    DESCRIPCION_PRODUCTO = document.getElementById('descripcion'),
    PRECIO_PRODUCTO = document.getElementById('precio_producto');
    IMAGEN_PRODUCTO = document.getElementById('imagen_producto');

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', async () => {
    loadTemplate();

    const DATA = await fetchData(USER_API, 'getUser');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        fillTable();
    } else {
        sweetAlert(2, DATA.error, null);
    }
});



SEARCH_FORM.addEventListener('submit', (event) => {
    event.preventDefault();
    const FORM = new FormData(SEARCH_FORM);
    fillTable(FORM);
});


SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se verifica la acción a realizar.
    (ID_PRODUCTO.value) ? action = 'updateRow' : action = 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(PRODUCTO_API, action, FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se cierra la caja de diálogo.
        SAVE_MODAL.hide();
        // Se muestra un mensaje de éxito.
        sweetAlert(1, DATA.message, true);
        // Se carga nuevamente la tabla para visualizar los cambios.
        fillTable();
    } else {
        sweetAlert(2, DATA.error, false);
    }
});


const fillTable = async (form = null) => {
    TABLE_BODY.innerHTML = '';
    (form) ? action = 'searchRows' : action = 'readAll';
    const DATA = await fetchData(PRODUCTO_API, action, form);
    if (DATA.status) {
        DATA.dataset.forEach(row => {

            TABLE_BODY.innerHTML += `
            <div class="col">
                <div class="card h-100 border-light">
                    <img src="${SERVER_URL}images/productos/${row.imagen_producto}" class="card-img-top" alt="..." loading="lazy">
                    <div class="card-body">
                        <h5 class="card-title">${row.nombre_producto}</h5>
                        <div class="descripcion-precio">    
                            <p class="card-text">$${row.precio_producto}</p>
                        </div>
                        <div class="botones-cards">
                            <button type="button" class="boton-editar" onclick="openUpdate(${row.id_producto})">
                                <img src="../../resources/img/icon-editar.svg" alt="">Editar producto
                            </button>

                            <button type="button" class="boton-eliminar" onclick="openDelete(${row.id_producto})">
                                <img src="../../resources/img/icon-eliminar.svg" alt="">Eliminar producto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            `;
        });
    } else {
        sweetAlert(4, DATA.error, true);
    }
}


const openCreate = () => {
    // Se muestra la caja de diálogo con su título.
    SAVE_MODAL.show();
    MODAL_TITLE.textContent = 'Crear producto';
    // Se prepara el formulario.
    SAVE_FORM.reset();
    fillSelectCategoria(CATEGORIA_API, 'readAll', 'categoriaProducto');
}


const openUpdate = async (id) => {
    // Se define un objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('id_producto', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(PRODUCTO_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar producto';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        //EXISTENCIAS_PRODUCTO.disabled = true;
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_PRODUCTO.value = ROW.id_producto;
        NOMBRE_PRODUCTO.value = ROW.nombre_producto;
        DESCRIPCION_PRODUCTO.value = ROW.descripcion;
        PRECIO_PRODUCTO.value = ROW.precio_producto;
        fillSelectCategoria(CATEGORIA_API, 'readAll', 'categoriaProducto', ROW.id_categoria);
        document.getElementById('imagePreview').src = `${SERVER_URL}images/productos/${ROW.imagen_producto}`;
    } else {
        sweetAlert(2, DATA.error, false);
    }
}

const openDelete = async (id) => {
    const RESPONSE = await confirmAction('¿Desea eliminar el producto de forma permanente?');
    if (RESPONSE) {
        const FORM = new FormData();
        FORM.append('id_producto', id);
        const DATA = await fetchData(PRODUCTO_API, 'deleteRow', FORM);
        if (DATA.status) {
            await sweetAlert(1, DATA.message, true);
            fillTable();
        } else {
            sweetAlert(2, DATA.error, false);
        }
    }
}

/*
*   Función para abrir un reporte automático de productos por categoría.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const openReport = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/private/productos.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}