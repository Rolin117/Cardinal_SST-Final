// Constante para completar la ruta de la API.
const ADMINISTRADOR_API = 'services/private/administrador.php';
// Constantes para establecer los elementos de la tabla.
const TABLE_BODY = document.getElementById('tableBody');
// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal(document.getElementById('exampleModal')),
    MODAL_TITLE = document.getElementById('exampleModalLabel');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    ID_ADMIN = document.getElementById('id_administrador'),
    NOMBRE_ADMIN = document.getElementById('nombre_admin'),
    APELLIDO_ADMIN = document.getElementById('apellido_admin'),
    TELEFONO_ADMIN = document.getElementById('telefono_admin'),
    CORREO_ADMIN = document.getElementById('correo_admin'),
    CONTRASENIA_ADMIN = document.getElementById('contrasenia_admin');



// const SAVE_MODAL2 = new bootstrap.Modal(document.getElementById('exampleModall')),
//     MODAL_TITLE2 = document.getElementById('exampleModalLabell');
// const
//     ID_ADMIN2 = document.getElementById('id_admin'),
//     NOMBRE_ADMIN2 = document.getElementById('nombreAdministradorr'),
//     APELLIDO_ADMIN2 = document.getElementById('apellidoAdministradorr'),
//     TELEFONO_ADMIN2 = document.getElementById('telefonoo'),
//     CORREO_ADMIN2 = document.getElementById('correoAdministradorr')

document.addEventListener('DOMContentLoaded', async () => {
    fillTable();

    // Petición para obtener los datos del usuario que ha iniciado sesión.
    const DATA = await fetchData(ADMINISTRADOR_API, 'readProfile');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        fillTable();
    } else {
        sweetAlert(2, DATA.error, null);
    }
});

// Método del evento para cuando se envía el formulario de buscar.
// SEARCH_FORM.addEventListener('submit', (event) => {
//     // Se evita recargar la página web después de enviar el formulario.
//     event.preventDefault();
//     // Constante tipo objeto con los datos del formulario.
//     const FORM = new FormData(SEARCH_FORM);
//     // Llamada a la función para llenar la tabla con los resultados de la búsqueda.
//     fillTable(FORM);
// });

// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se verifica la acción a realizar.
    (ID_ADMIN.value) ? action = 'updateRow' : action= 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(ADMINISTRADOR_API, action, FORM);
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

/*
*   Función asíncrona para llenar la tabla con los registros disponibles.
*   Parámetros: form (objeto opcional con los datos de búsqueda).
*   Retorno: ninguno.
*/
const fillTable = async (form = null) => {
    // Se inicializa el contenido de la tabla.
    TABLE_BODY.innerHTML = '';
    // Se verifica la acción a realizar.
    const action = form ? 'searchRows' : 'readAll';
    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(ADMINISTRADOR_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros fila por fila.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                             <tr>
                                <td scope="row">${row.id_administrador}</td>
                                <td>${row.nombre_admin}</td>
                                <td>${row.correo_admin}</td>
                                <td>${row.telefono_admin}</td>
                                <td>
                                    <button class="boton-accion boton-editar" onclick="openUpdate(${row.id_administrador})">
                                        <img src="../../resources/img/icon-editar.svg" alt="">
                                    </button>
                                    <button class="boton-accion boton-eliminar" onclick="openDelete(${row.id_administrador})">
                                        <img src="../../resources/img/icon-eliminar.svg" alt="">
                                    </button>
                                </td>
                            </tr>
            `;
        });
    } else {
        sweetAlert(4, DATA.error, true);
    }
}

/*
*   Función para preparar el formulario al momento de insertar un registro.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const openCreate = () => {
    SAVE_MODAL.show();
    MODAL_TITLE.textContent = 'Crear administrador';
    // Se prepara el formulario.
    SAVE_FORM.reset();
}

/*
*   Función asíncrona para preparar el formulario al momento de actualizar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openUpdate = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('id_administrador', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(ADMINISTRADOR_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar Usuario';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_ADMIN.value = ROW.id_administrador;
        NOMBRE_ADMIN.value = ROW.nombre_admin;
        APELLIDO_ADMIN.value = ROW.apellido_admin;
        TELEFONO_ADMIN.value = ROW.telefono_admin;
        CORREO_ADMIN.value = ROW.correo_admin;
        CONTRASENIA_ADMIN.value = ROW.contrasenia_admin;
    } else {
        sweetAlert(2, DATA.error, false);
    }
}

/*
*   Función asíncrona para eliminar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openDelete = async (id) => {
    // Llamada a la función para mostrar un mensaje de confirmación, capturando la respuesta en una constante.
    const RESPONSE = await confirmAction('¿Desea eliminar el usuario de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('id_administrador', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(ADMINISTRADOR_API, 'deleteRow', FORM);
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se muestra un mensaje de éxito.
            await sweetAlert(1, DATA.message, true);
            // Se carga nuevamente la tabla para visualizar los cambios.
            fillTable();
        } else {
            sweetAlert(2, DATA.error, false);
        }
    }
}

/*
*   Función para abrir un reporte parametrizado de productos de una categoría.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openReport = (id) => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/admin/productos_categoria.php`);
    // Se agrega un parámetro a la ruta con el valor del registro seleccionado.
    PATH.searchParams.append('id_administrador', id);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}
