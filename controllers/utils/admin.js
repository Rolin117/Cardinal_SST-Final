/*
*   Controlador de uso general en las páginas web del sitio privado.
*   Sirve para manejar la plantilla del encabezado y pie del documento.
*/

// Constante para completar la ruta de la API.
const USER_API = 'services/private/administrador.php';
// Constante para establecer el elemento del contenido principal.
const MAIN = document.querySelector('main');
MAIN.style.paddingTop = '75px';
MAIN.style.paddingBottom = '100px';
MAIN.classList.add('container');
// Se establece el título de la página web.
document.querySelector('title').textContent = 'Suministros y Servicios Técnicos - Dashboard';
// Constante para establecer el elemento del título principal.
const MAIN_TITLE = document.getElementById('mainTitle');
MAIN_TITLE.classList.add('text-center', 'py-3');

/*  Función asíncrona para cargar el encabezado y pie del documento.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const loadTemplate = async () => {
    // Petición para obtener en nombre del usuario que ha iniciado sesión.
    const DATA = await fetchData(USER_API, 'getUser');
    // Se verifica si el usuario está autenticado, de lo contrario se envía a iniciar sesión.
    if (DATA.session) {
        // Se comprueba si existe un alias definido para el usuario, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se agrega el encabezado de la página web antes del contenido principal.
            const HEADER = document.querySelector('header');
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
                        <li><a href="#" onclick="logOut()">Cerrar Sesión</a></li>
                    </ul>
                </div>
            `;
            document.querySelector('.menu-toggle').addEventListener('click', function() {
                document.querySelector('.navigation').classList.toggle('active');
            });

            // Se agrega el pie de la página web después del contenido principal.
            const FOOTER = document.querySelector('footer');
            FOOTER.innerHTML = `
                <div class="footer-content">
                    <div class="brand">
                        <h2 class="title">Cardinal</h2>
                        <span></span> <!-- Línea verde -->
                    </div> 
                    <div class="footer-nav">
                        <p class="email">info@Suministros y Servicios Técnicos.com</p>
                        <ul class="menu links">
                            <li><a href="dashboard.html">Inicio</a></li>
                            <li><a href="productos.html">Productos</a></li>
                            <li><a href="categorias.html">Categorías</a></li>
                            <li><a href="servicios.html">Servicios</a></li>
                            <li><a href="descuentos.html">Descuentos</a></li>
                        </ul>
                    </div>
                </div>
            `;
        } else {
            sweetAlert(3, DATA.error, false, 'index.html');
        }
    } else {
        // Se comprueba si la página web es la principal, de lo contrario se direcciona a iniciar sesión.
        if (location.pathname.endsWith('index.html')) {
            // Se agrega el encabezado de la página web antes del contenido principal.
            const HEADER = document.querySelector('header');
            HEADER.innerHTML = `
                <a href="index.html" class="logo"><img src="../../resources/img/logo.png" alt="" style="height: 45px; width: 45px;">Suministros y Servicios Técnicos</a>
                <button class="menu-toggle">
                    <img src="../../resources/img/menu.svg" alt="">
                </button>
                <div class="group">
                    <ul class="navigation">
                        <li><a href="index.html">Inicio</a></li>
                    </ul>
                </div>
            `;
            document.querySelector('.menu-toggle').addEventListener('click', function() {
                document.querySelector('.navigation').classList.toggle('active');
            });

            // Se agrega el pie de la página web después del contenido principal.
            const FOOTER = document.querySelector('footer');
            FOOTER.innerHTML = `
                <div class="footer-content">
                    <div class="brand">
                        <h2 class="title">Cardinal</h2>
                        <span></span> <!-- Línea verde -->
                    </div> 
                    <div class="footer-nav">
                        <p class="email">info@Suministros y Servicios Técnicos.com</p>
                        <ul class="menu links">
                            <li><a href="index.html">Inicio</a></li>
                        </ul>
                    </div>
                </div>
            `;
        } else {
            location.href = 'index.html';
        }
    }
}

// Llamada inicial para cargar las plantillas
loadTemplate();
