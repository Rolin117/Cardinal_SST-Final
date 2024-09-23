    let inactivityTime = function () {
        let time;
        // Función para restablecer el temporizador de inactividad
        window.onload = resetTimer;
        window.onmousemove = resetTimer;
        window.onmousedown = resetTimer; // Se incluye el evento para tocar la pantalla en dispositivos móviles
        window.ontouchstart = resetTimer;
        window.onclick = resetTimer;
        window.onkeypress = resetTimer;

        function logOut() {
            // Redirigir a la página de cierre de sesión
            fetch('services/private/administrador.php?action=logOut')
                .then(response => response.json())
                .then(data => {
                 if (data.status == 1) {
                        // Mostrar la SweetAlert al cerrar sesión
                        Swal.fire({
                            icon: 'info',
                            title: 'Sesión cerrada por inactividad',
                            text: data.message,
                            timer: 3000, // Tiempo de 3 segundos antes de redirigir
                            showConfirmButton: false
                        }).then(() => {
                            // Redirigir a la página de inicio de sesión
                            window.location.reload(true);
                        });
                    }else {
                        console.error(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error al cerrar la sesión:', error);
                });
        }

        function resetTimer() {
            clearTimeout(time);
            // Configurar el temporizador para cerrar la sesión después de 10 minutos de inactividad
            time = setTimeout(logOut, 600000); // 600000 ms = 10 minutos
        }
    };

    inactivityTime(); // Inicializa la función al cargar la página
