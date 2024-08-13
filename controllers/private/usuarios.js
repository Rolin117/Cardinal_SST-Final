const CLIENTE_API = 'services/private/administrador.php';
// Constantes para establecer los elementos de la tabla.
const TABLE_BODY = document.getElementById('tableBody');

const SAVE_MODAL = new bootstrap.Modal(document.getElementById('exampleModal')),
    MODAL_TITLE = document.getElementById('exampleModalLabel');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    ID_CLIENTE = document.getElementById('id_cliente'),
    NOMBRE_CLIENTE = document.getElementById('nombre_cliente'),
    APELLIDO_CLIENTE = document.getElementById('apellido_cliente'),
    TELEFONO_CLIENTE = document.getElementById('telefono_cliente'),
    CORREO_CLIENTE = document.getElementById('correo_cliente'),
    CONTRASENIA_CLIENTE = document.getElementById('contrasenia_cliente');

document.addEventListener('DOMContentLoaded', async () => {
    loadTemplate();

    // Petición para obtener los datos del usuario que ha iniciado sesión.
    const DATA = await fetchData(CLIENTE_API, 'getUser');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        fillTable();
    } else {
        sweetAlert(2, DATA.error, null);
    }
});


const fillTable = async (form = null) => {
    TABLE_BODY.innerHTML = '';
    (form) ? action = 'searchRowsC' : action = 'readAllC';
    const DATA = await fetchData(CLIENTE_API, action, form);
    if (DATA.status) {
        DATA.dataset.forEach(row => {

            TABLE_BODY.innerHTML +=
                `<tr>
                    <td scope="row">${row.id_cliente}</td>
                    <td>${row.nombre_cliente}</td>
                    <td>${row.correo_cliente}</td>
                    <td>${row.telefono_cliente}</td>
                    <td>
                        <button class="boton-accion boton-editar" onclick="openUpdate(${row.id_cliente})">
                            <img src="../../resources/img/info.png" alt="">
                        </button>
                        <button class="boton-accion boton-eliminar" onclick="openDelete(${row.id_cliente})">
                            <img src="../../resources/img/icon-eliminar.svg" alt="">
                        </button>
                    </td>
                </tr>`;
        });
    } else {
        sweetAlert(4, DATA.error, true);
    }
}

SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se verifica la acción a realizar.
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(CLIENTE_API, 'updateRowC', FORM);
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


const openUpdate = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('id_cliente', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(CLIENTE_API, 'readOneC', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Información del Usuario';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_CLIENTE.value = ROW.id_cliente;
        NOMBRE_CLIENTE.value = ROW.nombre_cliente;
        APELLIDO_CLIENTE.value = ROW.apellido_cliente;
        TELEFONO_CLIENTE.value = ROW.telefono_cliente;
        CORREO_CLIENTE.value = ROW.correo_cliente;
    } else {
        sweetAlert(2, DATA.error, false);
    }
}

const openDelete = async (id) => {
    // Llamada a la función para mostrar un mensaje de confirmación, capturando la respuesta en una constante.
    const RESPONSE = await confirmAction('¿Desea eliminar el usuario de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('id_cliente', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(CLIENTE_API, 'deleteRowC', FORM);
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

