const CLIENTE_API = 'services/private/administrador.php';
const PEDIDO_API = 'services/private/pedidos.php';

const TABLE_BODY = document.getElementById('tableBody');

const SAVE_MODAL = new bootstrap.Modal(document.getElementById('clientModal')),
    MODAL_TITLE = document.getElementById('clientModalLabel');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    ID_CLIENTE = document.getElementById('id_cliente'),
    NOMBRE_CLIENTE = document.getElementById('nombre_cliente'),
    TELEFONO_CLIENTE = document.getElementById('telefono_cliente'),
    CORREO_CLIENTE = document.getElementById('correo_cliente');



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
    (form) ? action = 'searchRows' : action = 'readAll';
    const DATA = await fetchData(PEDIDO_API, action, form);
    if (DATA.status) {
        DATA.dataset.forEach(row => {

            TABLE_BODY.innerHTML +=
                `<tr>
                                <td scope="row">${row.id_pedido}</td>
                                <td>${row.fecha}</td>
                                <td>${row.total}</td>
                                <td>${row.nombre_cliente}</td>
                                <td>
                                    <button class="boton-accion boton-editar" onclick="openCustomerInfo(${row.id_cliente})">
                                        <img src="../../resources/img/info.png" alt="Info">
                                    </button>
                                </td>
                            </tr>
    `;
        });
    } else {
        sweetAlert(4, DATA.error, true);
    }
}



const openCustomerInfo = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('id_pedido', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(PEDIDO_API, 'getClientePorPedido', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Información del Cliente';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_CLIENTE.value = ROW.id_cliente;
        NOMBRE_CLIENTE.value = ROW.nombre_cliente;
        TELEFONO_CLIENTE.value = ROW.telefono_cliente;
        CORREO_CLIENTE.value = ROW.correo_cliente;
    } else {
        sweetAlert(2, DATA.error, false);
        
    }
}
