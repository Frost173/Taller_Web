// actualizacion.js
document.addEventListener('DOMContentLoaded', function() {

    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const menuIcon = document.querySelector('.menu-icon');

    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('show-sidebar');
    });

    // Cerrar sidebar al hacer clic fuera
    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
            sidebar.classList.remove('show-sidebar');
        }
    });
    
    function actualizarContadores() {
        // Función para actualizar un elemento específico de manera segura
        function actualizarElemento(selector, valor, index = 0) {
            const elementos = document.querySelectorAll(selector);
            if (elementos && elementos[index]) {
                elementos[index].textContent = valor;
            }
        }

        // Obtener contadores generales
        fetch('contadores.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Actualizar cada contador en su ubicación correcta
                    actualizarElemento('.dashboard .card:nth-child(1) h3', data.data.estacionados);
                    actualizarElemento('.dashboard .card:nth-child(3) h3', data.data.categorias);
                    actualizarElemento('.dashboard .card:nth-child(4) h3', '$' + data.data.ganancias);
                    actualizarElemento('.dashboard .card:nth-child(5) h3', data.data.registros);
                    actualizarElemento('.dashboard .card:nth-child(6) h3', data.data.fecha_actual);
                }
            })
            .catch(error => {
                console.error('Error al actualizar contadores:', error);
            });

        // Obtener contador de salidas (vehículos partidos)
        fetch('contar_salida.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    actualizarElemento('.dashboard .card:nth-child(2) h3', data.total);
                }
            })
            .catch(error => {
                console.error('Error al actualizar contador de salidas:', error);
            });
    }

    // Realizar la primera actualización
    actualizarContadores();

    // Configurar actualizaciones periódicas
    setInterval(actualizarContadores, 5000);
});