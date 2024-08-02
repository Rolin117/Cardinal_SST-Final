const HEADER = document.querySelector('header');
const USER_API = 'services/private/administrador.php';



HEADER.innerHTML = `
<a href="index.html" class="logo"><img src="../../resources/img/logo.png" alt="" style="height: 45px; width: 45px;">Suministros y Servicios Técnicos</a>
<button class="menu-toggle">
<img src="../../resources/img/menu.svg" alt="">
</button> 
<div class="group">
    <ul class="navigation">
        <li><a href="index.html">Inicio</a></li>
        <li><a href="servicios.html">Servicios</a></li>
        <li><a href="contacto.html">Contacto</a></li>
        <li><a href="nosotros.html">Nosotros</a></li>
        <li><a href="historial-pedidos.html">Pedidos</a></li>
        <a href="carrito.html"><img src="../../resources/img/public/Cart.svg" alt="Carrito"></a>
        <li><a href="login.html">Iniciar sesión</a></li>
    </ul>
</div>
`;

/*Agregar perfil a la barra de navegacion y agregar validacion del usuario*/

