const HEADER = document.querySelector('header');
const USER_API = 'services/private/administrador.php';



HEADER.innerHTML = `
<a href="dashboard.html" class="logo"><img src="../../resources/img/logo.png" alt="" style="height: 45px; width: 45px;">Suministros y Servicios Técnicos</a>
<button class="menu-toggle">
<img src="../../resources/img/menu.svg" alt="">
</button>
<div class="group">
    <ul class="navigation">
        <li><a href="dashboard.html">Inicio</a></li>
        <li><a href="productos.html">Productos</a></li>
        <li><a href="categorias.html">Categorias</a></li>
        <li><a href="servicios.html">Servicios</a></li>
        <li><a href="descuentos.html">Descuentos</a></li>
        <li><a href=".html">Usuarios</a></li>
        <li><a href=".html">Administradores</a></li>
        <li><a href=".html">Pedidos</a></li>
        <li><a href=".html">Perfil</a></li>
        <li><a href="#" onclick="logOut()">Cerrar Sesión</a></li>
    </ul>
</div>
`;

document.querySelector('.menu-toggle').addEventListener('click', function() {
    document.querySelector('.navigation').classList.toggle('active');
});

const loadTemplate = async () => {
    // Petición para obtener en nombre del usuario que ha iniciado sesión.
    const DATA = await fetchData(USER_API, 'getUser');
    // Se verifica si el usuario está autenticado, de lo contrario se envía a iniciar sesión.
    if (DATA.session) {
        // Se comprueba si existe un alias definido para el usuario, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se agrega el encabezado de la página web antes del contenido principal.


        } else {
            sweetAlert(3, DATA.error, false, 'index.html');
        }
    } else {
        // Se comprueba si la página web es la principal, de lo contrario se direcciona a iniciar sesión.
        if (location.pathname.endsWith('index.html')) {
            // Se agrega el encabezado de la página web antes del contenido principal.
        } else {
            location.href = 'index.html';
        }
    }
}
