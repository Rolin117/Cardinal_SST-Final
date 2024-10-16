const PROFILE_FORM = document.getElementById('profileForm'),
    NOMBRE_ADMIN = document.getElementById('nombre_admin'),
    APELLIDO_ADMIN = document.getElementById('apellido_admin'),
    TELEFONO_ADMIN = document.getElementById('telefono_admin'),
    CORREO_ADMIN = document.getElementById('correo_admin');

const PASSWORD_MODAL = new bootstrap.Modal(document.getElementById('passwordModal'));
const PASSWORD_FORM = document.getElementById('passwordForm');

document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();

    // Petición para obtener los datos del usuario que ha iniciado sesión.
    const DATA = await fetchData(USER_API, 'readProfile');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se inicializan los campos del formulario con los datos del usuario que ha iniciado sesión.
        const ROW = DATA.dataset;
        NOMBRE_ADMIN.value = ROW.nombre_admin;
        APELLIDO_ADMIN.value = ROW.apellido_admin;
        TELEFONO_ADMIN.value = ROW.telefono_admin;
        CORREO_ADMIN.value = ROW.correo_admin;
    } else {
        sweetAlert(2, DATA.error, null);
    }
});

PROFILE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    
    // Pregunta de confirmación para editar el perfil.
    const RESPONSE = await confirmAction('¿Está seguro de que desea editar su perfil?');
    
    // Si el usuario confirma, se procede con la actualización del perfil.
    if (RESPONSE) {
        // Constante tipo objeto con los datos del formulario.
        const FORM = new FormData(PROFILE_FORM);
        
        // Petición para actualizar los datos personales del usuario.
        const DATA = await fetchData(USER_API, 'editProfile', FORM);
        
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            sweetAlert(1, DATA.message, true);
        } else {
            sweetAlert(2, DATA.error, false);
        }
    }
});


PASSWORD_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(PASSWORD_FORM);
    // Petición para actualizar la constraseña.
    const DATA = await fetchData(USER_API, 'changePassword', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se cierra la caja de diálogo.
        PASSWORD_MODAL.hide();
        // Se muestra un mensaje de éxito.
        sweetAlert(1, DATA.message, true);
    } else {
        sweetAlert(2, DATA.error, false);
    }
});


const openPassword = () => {
    // Se abre la caja de diálogo que contiene el formulario.
    PASSWORD_MODAL.show();
    // Se restauran los elementos del formulario.
    PASSWORD_FORM.reset();
}

document.getElementById('telefono_admin').addEventListener('input', function (e) {
    var x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,4})/);
    e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
});