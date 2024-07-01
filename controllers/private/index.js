const USER_API = 'services/private/administrador.php';
// Constante para establecer el formulario de inicio de sesión.
const LOGIN_FORM = document.getElementById('loginform');

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', async () => {
     // Petición para consultar los usuarios registrados.
     const DATA = await fetchData(USER_API, 'readUsers');
     // Se comprueba si existe una sesión, de lo contrario se sigue con el flujo normal.
     if (DATA.session) {
         // Se direcciona a la página web de bienvenida.
         location.href = 'dashboard.html';
     } else if (DATA.status) {
         // Se muestra el formulario para iniciar sesión.
         LOGIN_FORM.classList.remove('d-none');
         sweetAlert(4, DATA.message, true);
     } else {;
         // Se muestra el formulario para registrar el primer usuario.
         location.href = 'primer_uso.html';
         sweetAlert(4, DATA.error, true);
     }
 });


// Método del evento para cuando se envía el formulario de inicio de sesión.
LOGIN_FORM.addEventListener('submit', async (event) => {
     // Se evita recargar la página web después de enviar el formulario.
     event.preventDefault();
     // Constante tipo objeto con los datos del formulario.
     const FORM = new FormData(LOGIN_FORM);
     // Petición para iniciar sesión.
     const DATA = await fetchData(USER_API, 'logIn', FORM);
     // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
     if (DATA.status) {
         sweetAlert(1, DATA.message, true, 'dashboard.html');
     } else {
         sweetAlert(2, DATA.error, false);
     }
 });

 LOGIN_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(LOGIN_FORM);
    // Petición para iniciar sesión.
    const DATA = await fetchData(USER_API, 'logIn', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        sweetAlert(1, DATA.message, true, 'dashboard.html');
    } else {
        sweetAlert(2, DATA.error, false);
    }
});