// Variables globales
let currentPage = 1;
let rowsPerPage = 5;
let isSubmitting = false;
let vehiclesData = [];

// Event listener para DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    console.log("Inicializando aplicación...");
    initializeApp();
});

function initializeApp() {
    try {
        actualizarTablaVehiculos();
        
        // Inicializar el formulario
        const form = document.querySelector('#addVehicleForm');
        if (form) {
            form.addEventListener('submit', addVehicle);
        } else {
            console.error('Error: Formulario no encontrado en el DOM');
        }
        
        // Inicializar controles de paginación
        initializePaginationControls();
        
    } catch (error) {
        console.error('Error en la inicialización:', error);
        mostrarError('Error al inicializar la aplicación');
    }
}

function actualizarTablaVehiculos() {
    console.log("Iniciando actualización de tabla de vehículos...");
    
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'loading-indicator';
    loadingIndicator.textContent = 'Cargando...';
    
    const vehicleTable = document.querySelector("#vehicle-table tbody");
    if (vehicleTable) {
        vehicleTable.innerHTML = ''; // Limpiar tabla
        vehicleTable.appendChild(loadingIndicator);
    }

    fetch('obtener_vehiculos.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(response => {
            if (!response.success) {
                throw new Error(response.error || 'Error desconocido');
            }

            vehiclesData = response.data;
            console.log("Datos recibidos:", data);

            if (!vehicleTable) {
                throw new Error('Tabla de vehículos no encontrada');
            }

            // Limpiar la tabla
            vehicleTable.innerHTML = '';

            if (!Array.isArray(vehiclesData) || vehiclesData.length === 0) {
                vehicleTable.innerHTML = '<tr><td colspan="6" class="text-center">No hay vehículos registrados</td></tr>';
                return;
            }

            // Crear filas de la tabla
            vehiclesData.forEach((vehicle, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${sanitizeHTML(vehicle.placa)}</td>
                    <td>${sanitizeHTML(vehicle.fecha_entrada)}</td>
                    <td>${sanitizeHTML(vehicle.hora_entrada)}</td>
                    <td>${sanitizeHTML(vehicle.estado)}</td>
                    <td>
                        <button class="btn-action" onclick="imprimirInformacion(${vehicle.id})">
                            Imprimir
                        </button>
                    </td>
                `;
                vehicleTable.appendChild(row);
            });

            // Actualizar la paginación
            displayPage(1);
            updateVehicleCounts();
            console.log("Tabla actualizada exitosamente");

        })
        .catch(error => {
            console.error('Error al actualizar la tabla:', error);
            mostrarError('Error al cargar los datos de vehículos');
            
            if (vehicleTable) {
                vehicleTable.innerHTML = '<tr><td colspan="6" class="text-center">Error al cargar los datos</td></tr>';
            }
        })
        .finally(() => {
            if (loadingIndicator) {
                loadingIndicator.remove();
            }
        });
}

// Función para actualizar los contadores de tipos de vehículos
function updateVehicleCounts() {
    const counts = {
        'Auto': 0,
        'Motocicleta': 0,
        'Mini Furgoneta': 0,
        'Camioneta': 0,
        'Minibus': 0,
        'Camión': 0
    };

    vehiclesData.forEach(vehicle => {
        if (counts.hasOwnProperty(vehicle.tipo)) {
            counts[vehicle.tipo]++;
        }
    });

    // Actualizar los contadores en el DOM
    const countElements = document.querySelectorAll('.current-count');
    countElements.forEach((element, index) => {
        const tipo = element.closest('li').querySelector('.vehicle-type').textContent
            .replace('Limite de ', '')
            .replace(':', '')
            .trim();
            
        // Mapear los nombres de tipos a las claves en counts
        const mappedTipo = tipo === 'Coches' ? 'Auto' :
                          tipo === 'Mini Van' ? 'Mini Furgoneta' :
                          tipo === 'Furgonetas' ? 'Camioneta' :
                          tipo === 'Minibús' ? 'Minibus' :
                          tipo;
                          
        element.textContent = counts[mappedTipo] || 0;
    });
}


function addVehicle(event) {
    event.preventDefault();
    console.log("Iniciando proceso de agregar vehículo...");

    if (isSubmitting) {
        console.log("Evitando envío múltiple del formulario");
        return;
    }

    isSubmitting = true;
    
    const form = document.getElementById('addVehicleForm');
    const submitButton = form.querySelector('button[type="submit"]');
    
    if (submitButton) {
        submitButton.disabled = true;
    }

    // Recoger datos del formulario
    const formData = {
        placa: document.getElementById('placa').value.trim(),
        tipo: document.getElementById('tipo').value,
        precio: document.getElementById('precio').value
    };

    // Validación de datos
    if (!validarDatosVehiculo(formData)) {
        isSubmitting = false;
        if (submitButton) {
            submitButton.disabled = false;
        }
        return;
    }

    console.log("Enviando datos:", formData);

    fetch('agregar_vehiculo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarExito("Vehículo agregado correctamente");
            form.reset();
            
            // Imprimir ticket y actualizar tabla
            if (data.id_estacionamiento) {
                imprimirInformacion(data.id_estacionamiento);
            }
            
            actualizarTablaVehiculos();
        } else {
            throw new Error(data.error || 'Error desconocido');
        }
    })
    .catch(error => {
        console.error('Error en fetch:', error);
        mostrarError(error.message);
    })
    .finally(() => {
        isSubmitting = false;
        if (submitButton) {
            submitButton.disabled = false;
        }
    });
}

function validarDatosVehiculo(data) {
    if (!data.placa || !data.tipo || !data.precio) {
        mostrarError("Por favor, complete todos los campos");
        return false;
    }

    // Obtener el contador actual para el tipo seleccionado
    const tipoMapped = {
        'Auto': 'Coches',
        'Motocicleta': 'Motocicletas',
        'Mini Furgoneta': 'Mini Van',
        'Camioneta': 'Furgonetas',
        'Minibus': 'Minibús',
        'Camión': 'Camión'
    };

    const tipoTexto = tipoMapped[data.tipo];
    const limitElement = Array.from(document.querySelectorAll('.vehicle-type'))
        .find(el => el.textContent.includes(tipoTexto));

    if (limitElement) {
        const countElement = limitElement.closest('li').querySelector('.current-count');
        const maxCount = parseInt(limitElement.closest('li').textContent.split('de')[1].trim());
        const currentCount = parseInt(countElement.textContent);

        if (currentCount >= maxCount) {
            mostrarError(`No hay espacios disponibles para ${tipoTexto}`);
            return false;
        }
    }

    return true;
}


function imprimirInformacion(idEstacionamiento) {
    console.log("Iniciando impresión para estacionamiento ID:", idEstacionamiento);
    
    fetch(`imprimir_ticket.php?id=${idEstacionamiento}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.text();
        })
        .then(html => {
            // Crear una ventana nueva para la impresión
            const ventanaImpresion = window.open('', '_blank');
            ventanaImpresion.document.write(html);
            ventanaImpresion.document.close();
        })
        .catch(error => {
            console.error('Error al imprimir:', error);
            mostrarError('Error al generar el ticket de impresión');
        });
}

// Funciones de paginación
function displayPage(page) {
    const rows = Array.from(document.querySelectorAll("#vehicle-table tbody tr"));
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach((row, index) => {
        row.style.display = (index >= start && index < end) ? '' : 'none';
    });

    currentPage = page;
    updatePaginationControls(rows.length);
}

function updatePaginationControls(totalRows) {
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    const paginationSpan = document.querySelector('.table-pagination span');
    const prevButton = document.querySelector('.table-pagination button:first-child');
    const nextButton = document.querySelector('.table-pagination button:last-child');

    if (paginationSpan) {
        paginationSpan.textContent = `Página ${currentPage} de ${totalPages}`;
    }
    
    if (prevButton) prevButton.disabled = (currentPage === 1);
    if (nextButton) nextButton.disabled = (currentPage === totalPages);
}

function changeEntries() {
    rowsPerPage = parseInt(document.getElementById('entries').value);
    currentPage = 1;
    displayPage(currentPage);
}

function nextPage() {
    const totalRows = document.querySelectorAll("#vehicle-table tbody tr").length;
    if (currentPage * rowsPerPage < totalRows) {
        displayPage(currentPage + 1);
    }
}

function previousPage() {
    if (currentPage > 1) {
        displayPage(currentPage - 1);
    }
}

function searchTable() {
    const input = document.getElementById('search-input').value.toLowerCase();
    const rows = document.querySelectorAll("#vehicle-table tbody tr");

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });

    currentPage = 1;
    displayPage(currentPage);
}

function sanitizeHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function mostrarError(mensaje) {
    alert(mensaje);  // Puedes reemplazar esto con una mejor UI para mostrar errores
}

function mostrarExito(mensaje) {
    alert(mensaje);  // Puedes reemplazar esto con una mejor UI para mostrar éxitos
}