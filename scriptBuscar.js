// scriptBuscar
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    mobileMenuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', (event) => {
        if (!sidebar.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    });

    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const searchResults = document.getElementById('searchResults');
    const errorMessage = document.getElementById('errorMessage');

    let timeoutId;

    // Función para formatear fecha y hora
    function formatDateTime(fecha, hora) {
        if (!fecha) return 'N/A';
        const date = new Date(fecha + ' ' + hora);
        return new Intl.DateTimeFormat('es-ES', {
            dateStyle: 'medium',
            timeStyle: 'short'
        }).format(date);
    }

    // Función para formatear moneda
    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(amount);
    }

    // Función para mostrar error
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 5000);
    }

    // Función para realizar la búsqueda
    async function realizarBusqueda() {
        const searchTerm = searchInput.value.trim();
        
        if (searchTerm.length < 3) {
            showError('Por favor, ingrese al menos 3 caracteres para buscar');
            return;
        }

        try {
            const response = await fetch(`buscar_vehiculo.php?placa=${encodeURIComponent(searchTerm)}`);
            const data = await response.json();

            if (data.error) {
                showError(data.error);
                return;
            }

            if (data.data.length === 0) {
                searchResults.innerHTML = '<p>No se encontraron resultados</p>';
                return;
            }

            // Mostrar resultados
            searchResults.innerHTML = data.data.map(item => `
                <div class="result-item">
                    <h3>Vehículo: ${item.placa}</h3>
                    <div class="result-details">
                        <div>
                            <strong>Tipo:</strong> ${item.tipo}
                        </div>
                        <div>
                            <strong>Entrada:</strong> ${formatDateTime(item.fecha_entrada, item.hora_entrada)}
                        </div>
                        <div>
                            <strong>Salida:</strong> 
                            ${item.fecha_salida ? formatDateTime(item.fecha_salida, item.hora_salida) : 'En estacionamiento'}
                        </div>
                        <div>
                            <strong>Estado:</strong> 
                            <span class="status-badge ${item.estado === 'Partio' ? 'status-partio' : 'status-estacionado'}">
                                ${item.estado}
                            </span>
                        </div>
                        <div>
                            <strong>Pago:</strong> 
                            <span class="status-badge status-${item.estado_pago}">
                                ${item.estado_pago === 'completo' ? 'Pagado' : 'Pendiente'}
                            </span>
                        </div>
                        <div>
                            <strong>Tarifa:</strong> ${formatCurrency(item.precio)}
                        </div>
                        ${item.monto ? `
                            <div>
                                <strong>Monto total:</strong> ${formatCurrency(item.monto)}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `).join('');

        } catch (error) {
            showError('Error al realizar la búsqueda');
            console.error('Error:', error);
        }
    }

    // Event listeners
    searchButton.addEventListener('click', realizarBusqueda);

    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            realizarBusqueda();
        } else {
            // Implementar debounce para búsqueda automática
            clearTimeout(timeoutId);
            timeoutId = setTimeout(realizarBusqueda, 500);
        }
    });
});