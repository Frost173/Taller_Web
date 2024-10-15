/**
 * scriptSalida.js
 * Script para manejar la sección de salidas del sistema de estacionamiento
 */

let currentPage = 1;
let rowsPerPage = 10;
let vehiculosPartidos = 0;
const logErrores = []; // Array para almacenar los errores



// Inicializar la aplicación cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    try {
        actualizarTablaSalidas();
        
        // Configurar event listeners
        const entriesSelect = document.getElementById('espectaculo');
        if (entriesSelect) {
            entriesSelect.addEventListener('change', changeEntries);
        } else {
            throw new Error('Elemento "espectaculo" no encontrado');
        }

        const searchInput = document.querySelector('input[name="buscar"]');
        if (searchInput) {
            searchInput.addEventListener('input', searchTable);
        } else {
            throw new Error('Input de búsqueda no encontrado');
        }
    } catch (error) {
        logError('Error en la inicialización de la aplicación', error);
    }
});

/**
 * Obtiene y muestra los vehículos en la tabla de salidas
 */
function actualizarTablaSalidas() {
    fetch('obtener_salidas.php')
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        const tablaVehiculos = document.querySelector('.vehicles-table tbody');
        tablaVehiculos.innerHTML = '';
        
        data.forEach(vehiculo => {
            const row = tablaVehiculos.insertRow();
            const fechaEntrada = new Date(vehiculo.fecha_hora).toLocaleString();
            
            row.innerHTML = `
                <td>${vehiculo.id}</td>
                <td>${vehiculo.placa}</td>
                <td>${vehiculo.tipo}</td>
                <td>${vehiculo.estacionamiento}</td>
                <td>$${vehiculo.precio}</td>
                <td>${fechaEntrada}</td>
                <td><span class="status ${vehiculo.estado.toLowerCase()}" id="status-${vehiculo.id}">${vehiculo.estado}</span></td>
                <td>
                    <button 
                        class="action-btn"
                        id="btn-${vehiculo.id}"
                        onclick="registrarSalida(${vehiculo.id})"
                        ${vehiculo.estado === 'Partio' ? 'disabled' : ''}
                    >
                        Hecho
                    </button>
                </td>
            `;
        });
        
        displayPage(currentPage);
        actualizarContadorSalidas();
    })
    .catch(error => {
        console.error('Error al obtener vehículos:', error);
        logError('Error al obtener vehículos', error);
    });
}

/**
 * Registra la salida de un vehículo
 * @param {number} id - ID del vehículo
 */
function registrarSalida(id) {
    const confirmacion = confirm('¿Confirmar la salida del vehículo?');
    if (!confirmacion) return;

    const fechaSalida = new Date().toISOString().slice(0, 19).replace('T', ' ');

    fetch('actualizar_salida.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            fecha_salida: fechaSalida,
            estado: 'Partio'
        })
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json().then(data => {
                if (!response.ok) {
                    throw new Error(data.error || `HTTP error! status: ${response.status}`);
                }
                return data;
            });
        } else {
            return response.text().then(text => {
                throw new Error(`Respuesta no válida del servidor: ${text}`);
            });
        }
    })
    .then(data => {
        if (data.success) {
            vehiculosPartidos++;
            actualizarEstadoVehiculo(id);
            actualizarContadorSalidas();
        } else {
            throw new Error(data.error || 'Error desconocido al registrar la salida');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        logError('Error al registrar la salida', error);
        alert('Error al registrar la salida: ' + error.message);
    });
}

function logError(message, error = null) {
    const logData = {
        message: message,
        error: error ? error.toString() : null,
        timestamp: new Date().toISOString(),
        userAgent: navigator.userAgent,
        url: window.location.href
    };

    fetch('log_error.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(logData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            console.error('Error al guardar el log en el servidor:', data.error);
        }
    })
    .catch(error => console.error('Error al enviar el log:', error));
}

/**
 * Actualiza el estado del vehículo en la interfaz
 * @param {number} id - ID del vehículo
 */
function actualizarEstadoVehiculo(id) {
    const statusElement = document.getElementById(`status-${id}`);
    const btnElement = document.getElementById(`btn-${id}`);
    
    if (statusElement && btnElement) {
        statusElement.textContent = 'Partio';
        statusElement.className = 'status partio';
        btnElement.disabled = true;
    }
}

/**
 * Actualiza el contador de vehículos que han partido
 */
function actualizarContadorSalidas() {
    fetch('contar_salida.php')
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const contadorElement = document.getElementById('contador-salidas');
            if (contadorElement) {
                contadorElement.textContent = data.total;
            }
        } else {
            console.error('Error al obtener el contador:', data.error);
        }
    })
    .catch(error => console.error('Error al obtener contador:', error));
}

/**
 * Muestra una página específica de la tabla
 * @param {number} page - Número de página a mostrar
 */
function displayPage(page) {
    const tablaVehiculos = document.querySelector('.vehicles-table tbody');
    const rows = Array.from(tablaVehiculos.rows);
    const totalRows = rows.length;
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach((row, index) => {
        if (index >= start && index < end) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    actualizarControlesPaginacion(totalRows);
}

/**
 * Actualiza los controles de paginación
 * @param {number} totalRows - Número total de filas
 */
function actualizarControlesPaginacion(totalRows) {
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    
    // Actualizar el texto de paginación
    const paginacionInfo = document.querySelector('.pagination span');
    if (paginacionInfo) {
        paginacionInfo.textContent = `Mostrando ${1 + ((currentPage - 1) * rowsPerPage)} a ${Math.min(currentPage * rowsPerPage, totalRows)} de ${totalRows} entradas`;
    }

    // Actualizar estado de los botones de paginación
    const btnAnterior = document.querySelector('.pagination-btn:first-child');
    const btnSiguiente = document.querySelector('.pagination-btn:last-child');
    
    if (btnAnterior) btnAnterior.disabled = currentPage === 1;
    if (btnSiguiente) btnSiguiente.disabled = currentPage === totalPages;
}

/**
 * Cambia el número de entradas por página
 */
function changeEntries() {
    const entriesSelect = document.getElementById('espectaculo');
    rowsPerPage = parseInt(entriesSelect.value);
    currentPage = 1;
    displayPage(currentPage);
}

/**
 * Realiza la búsqueda en la tabla
 */
function searchTable() {
    const searchInput = document.querySelector('input[name="buscar"]');
    const filtro = searchInput.value.toLowerCase();
    const tablaVehiculos = document.querySelector('.vehicles-table tbody');
    const rows = tablaVehiculos.getElementsByTagName('tr');

    Array.from(rows).forEach(row => {
        const texto = row.textContent.toLowerCase();
        row.style.display = texto.includes(filtro) ? '' : 'none';
    });
}

// Funciones de navegación
function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        displayPage(currentPage);
    }
}

function nextPage() {
    const tablaVehiculos = document.querySelector('.vehicles-table tbody');
    const totalRows = tablaVehiculos.rows.length;
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    
    if (currentPage < totalPages) {
        currentPage++;
        displayPage(currentPage);
    }
}