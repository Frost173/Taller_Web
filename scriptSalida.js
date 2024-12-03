/**
 * scriptSalida.js
 * Script para manejar la sección de salidas del sistema de estacionamiento
 */
let currentPage = 1;
let rowsPerPage = 10;
// Inicializar la aplicación cuando el DOM esté cargado
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

    try {
        actualizarTablaSalidas();
        initializeEventListeners();
    } catch (error) {
        logError('Error en la inicialización de la aplicación', error);
    }
});
function initializeEventListeners() {
    const entriesSelect = document.getElementById('espectaculo');
    if (entriesSelect) {
        entriesSelect.addEventListener('change', changeEntries);
    }
    const searchInput = document.querySelector('input[name="buscar"]');
    if (searchInput) {
        searchInput.addEventListener('input', searchTable);
    }
}
/**
 * Obtiene y muestra los vehículos en la tabla de salidas
 */
async function actualizarTablaSalidas() {
    try {
        const response = await fetch('obtener_salidas.php');        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Respuesta no JSON recibida:', text);
            throw new Error(`La respuesta del servidor no es JSON válido. Contenido recibido: ${text.substring(0, 150)}...`);
        }
        const vehiculos = await response.json();        
        if (!Array.isArray(vehiculos)) {
            throw new Error('Los datos recibidos no son un array de vehículos');
        }
        const tablaVehiculos = document.querySelector('.vehicles-table tbody');
        if (!tablaVehiculos) {
            throw new Error('No se encontró la tabla de vehículos en el DOM');
        }
        tablaVehiculos.innerHTML = '';        
        vehiculos.forEach(vehiculo => {
            const row = tablaVehiculos.insertRow();            
            try {
                // Formateo de fechas mejorado para manejar la zona horaria de Perú
                const fechaSalida = vehiculo.fecha_salida ? 
                    new Date(vehiculo.fecha_salida + 'T00:00:00-05:00').toLocaleDateString('es-PE', {
                        timeZone: 'America/Lima'
                    }) : '-';
                
                const horaSalida = vehiculo.hora_salida || '-';
                
                row.innerHTML = `
                    <td>${sanitizeHTML(vehiculo.id)}</td>
                    <td>${sanitizeHTML(vehiculo.placa)}</td>
                    <td>${sanitizeHTML(vehiculo.tipo)}</td>
                    <td>$${parseFloat(vehiculo.precio || 0).toFixed(2)}</td>
                    <td>${fechaSalida}</td>
                    <td>${horaSalida}</td>
                    <td>
                        <span class="status ${(vehiculo.estado || '').toLowerCase()}"
                              id="status-${vehiculo.id}">
                            ${sanitizeHTML(vehiculo.estado || 'Desconocido')}
                        </span>
                    </td>
                    <td>
                        <button 
                            class="action-btn"
                            id="btn-${vehiculo.id}"
                            onclick="registrarSalida(${vehiculo.id})"
                            ${vehiculo.estado === 'Partio' ? 'disabled' : ''}>
                            Hecho
                        </button>
                    </td>
                `;
            } catch (error) {
                logError('Error al procesar vehículo', { vehiculo, error });
                row.innerHTML = '<td colspan="8">Error al procesar esta entrada</td>';
            }
        });        
        displayPage(currentPage);
        actualizarContadorSalidas();
        const errorMessage = document.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    } catch (error) {
        console.error('Error al obtener vehículos:', error);        
        showErrorMessage(`Error al cargar los datos: ${error.message}`);
    }
}
/**
 * Registra la salida de un vehículo
 * @param {number} id - ID del vehículo
 */
function registrarSalida(id) {
    // Obtener el botón y deshabilitarlo inmediatamente
    const btn = document.getElementById(`btn-${id}`);
    if (!btn || btn.disabled) {
        return; // Si el botón no existe o ya está deshabilitado, no hacer nada
    }    
    if (!confirm('¿Confirmar la salida del vehículo?')) {
        return;
    }    
    // Deshabilitar el botón
    btn.disabled = true;    
    fetch('actualizar_salida.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || 'Error al procesar la solicitud');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            actualizarEstadoVehiculo(id);
            actualizarContadorSalidas();
            actualizarTablaSalidas();            
            // Mostrar mensaje de éxito
            if (data.message) {
                alert(data.message);
            }
        } else {
            throw new Error(data.error || 'Error desconocido');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        logError('Error al registrar la salida', error);
        alert('Error al registrar la salida: ' + error.message);        
        // Re-habilitar el botón en caso de error
        btn.disabled = false;
    });
}
function handleJsonResponse(response) {
    const contentType = response.headers.get("content-type");
    if (contentType && contentType.indexOf("application/json") !== -1) {
        return response.json().then(data => {
            if (!response.ok) throw new Error(data.error || `HTTP error! status: ${response.status}`);
            return data;
        });
    }
    return response.text().then(text => {
        throw new Error(`Respuesta no válida del servidor: ${text}`);
    });
}
// Función mejorada de sanitización
function sanitizeHTML(str) {
    if (str === null || str === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(str);
    return div.innerHTML;
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
function logError(message, error = null) {
    const errorDetails = {
        message,
        timestamp: new Date().toISOString(),
        url: window.location.href,
        userAgent: navigator.userAgent
    };

    if (error) {
        errorDetails.errorMessage = error.message;
        errorDetails.errorStack = error.stack;
    }

    console.error('Error Log:', errorDetails);
}
/**
 * Función para mostrar mensajes de error en la interfaz
 */
function showErrorMessage(message) {
    // Remover cualquier mensaje de error previo
    const existingError = document.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.cssText = 'background-color: #ffebee; color: #c62828; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ef9a9a;';
    errorDiv.textContent = message;
    const tabla = document.querySelector('.vehicles-table');
    if (tabla) {
        tabla.parentNode.insertBefore(errorDiv, tabla);
    }
}