const form = document.querySelector('form');

form.addEventListener('submit', (event) => {
  event.preventDefault();

  const nombre = document.getElementById('nombre').value;
  const contrasena = document.getElementById('contrasena').value;
  const confirmarContrasena = document.getElementById('confirmarContrasena').value;
  const apellido = document.getElementById('apellido').value;
  const correo = document.getElementById('correo').value;
  const telefono = document.getElementById('telefono').value;

  if (contrasena !== confirmarContrasena) {
    alert('Las contraseñas no coinciden');
    return;
  }



  // Enviar datos al servidor o realizar otras acciones
  console.log('Formulario enviado:', {
    nombre,
    contrasena,
    apellido,
    correo,
    telefono
  });
});