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
        <li><a href="#" onclick="logOut()">Cerrar Sesión</a></li>
    </ul>
</div>
`;

document.querySelector('.menu-toggle').addEventListener('click', function() {
    document.querySelector('.navigation').classList.toggle('active');
});
