let currentPage = 1;
let rowsPerPage = 5;
let isSubmitting = false;

// Un solo event listener para DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    actualizarTablaVehiculos();
    
    // Inicializar el formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', addVehicle);
    } else {
        console.error('El formulario no se encontró en el DOM');
    }
    
    // Inicializar la paginación
    const entriesSelect = document.getElementById('entries');
    if (entriesSelect) {
        entriesSelect.addEventListener('change', changeEntries);
    }
    
    // Inicializar la búsqueda
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', searchTable);
    }
}

function actualizarTablaVehiculos() {
    console.log("Actualizando tabla de vehículos...");

    fetch('obtener_vehiculos.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(response => {
        if (!response.success) {
            throw new Error(response.error || 'Error desconocido');
        }

        const data = response.data;
        console.log("Datos recibidos:", data);

        const vehicleTable = document.querySelector("#vehicle-table tbody");
        if (!vehicleTable) {
            console.error("No se encontró la tabla de vehículos");
            return;
        }

        // Limpiar la tabla
        vehicleTable.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
            vehicleTable.innerHTML = '<tr><td colspan="6" class="text-center">No hay vehículos registrados</td></tr>';
            return;
        }

        // Agregar los nuevos datos
        data.forEach(vehiculo => {
            const row = vehicleTable.insertRow();
            row.innerHTML = `
                <td>${vehiculo.id || ''}</td>
                <td>${vehiculo.placa || ''}</td>
                <td>${vehiculo.estacionamiento || ''}</td>
                <td>${vehiculo.fecha_hora || ''}</td>
                <td><span class="vehicle-status">${vehiculo.estado || ''}</span></td>
                <td>
                    <button onclick="imprimirInformacion(${vehiculo.id})" 
                            class="print-btn">
                        Imprimir
                    </button>
                </td>
            `;
        });

        // Actualizar la paginación
        displayPage(1);
    })
    .catch(error => {
        console.error('Error al actualizar la tabla:', error);
        const vehicleTable = document.querySelector("#vehicle-table tbody");
        if (vehicleTable) {
            vehicleTable.innerHTML = '<tr><td colspan="6" class="text-center">Error al cargar los datos</td></tr>';
        }
    });
}

function addVehicle(event) {
    event.preventDefault();
    console.log("El comportamiento por defecto ha sido prevenido");

    // Evitar múltiples envíos
    if (isSubmitting) {
        return;
    }

    isSubmitting = true;
    
    // Obtener el formulario y el botón de envío
    const form = document.getElementById('addVehicleForm');
    const submitButton = form.querySelector('button[type="submit"]') || 
                        form.querySelector('input[type="submit"]') ||
                        document.querySelector('#addVehicleForm button');
    // Deshabilitar el botón si existe
    if (submitButton) {
        submitButton.disabled = true;
    }
    

    // Recoger datos del formulario
    const placa = document.getElementById('placa').value.trim();
    const tipo = document.getElementById('tipo').value;
    const estacionamiento = document.getElementById('numero-estacionamiento').value;
    const precio = document.getElementById('precio').value;
    const fechaHora = new Date().toISOString().slice(0, 19).replace('T', ' ');
    const estado = "Estacionado";

    // Validar que todos los campos estén llenos
    if (!placa || !tipo || !estacionamiento || !precio) {
        alert("Por favor, complete todos los campos.");
        console.error("Campos incompletos:", { placa, tipo, estacionamiento, precio });
        return;
    }

    console.log("Datos recogidos:", { placa, tipo, estacionamiento, precio, fechaHora, estado });

    // Crear objeto con los datos del vehículo
    const vehicleData = {
        placa,
        tipo,
        estacionamiento,
        precio,
        fechaHora,
        estado
    };

    console.log("Enviando datos:", vehicleData);

    // Enviar datos al servidor
    fetch('agregar_vehiculo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(vehicleData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Vehículo agregado correctamente");
            form.reset();
            // Esperamos un momento antes de actualizar la tabla
            setTimeout(actualizarTablaVehiculos, 100);
        } else if (data.error && data.error.includes('Duplicate entry')) {
            // Si es un error de duplicado, mostramos un mensaje más amigable
            alert("Este vehículo ya está registrado para hoy");
        } else {
            alert("Error al agregar vehículo");
        }
    })
    .catch((error) => {
        console.error('Error en fetch:', error);
        alert("Error de conexión al agregar vehículo");
    })
    .finally(() => {
        isSubmitting = false;
        if (submitButton) {
            submitButton.disabled = false;
        }
    });
}

// Función para mostrar una página específica
function displayPage(page) {
    console.log("Función displayPage llamada con página:", page);
    const vehicleTable = document.querySelector("#vehicle-table tbody");
    const rows = Array.from(vehicleTable.rows);
    const totalRows = rows.length;
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    console.log("Total de filas:", totalRows);
    console.log("Mostrando filas desde", start, "hasta", end);

    rows.forEach((row, index) => {
        row.style.display = (index >= start && index < end) ? '' : 'none';
    });

    currentPage = page;
    updatePaginationControls(totalRows);    
}
    // Habilitar/deshabilitar botones de paginación según corresponda
function updatePaginationControls(totalRows) {
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    
    // Actualizar el texto de la paginación
    const paginationSpan = document.querySelector('.table-pagination span');
    if (paginationSpan) {
        paginationSpan.innerText = `Página ${currentPage} de ${totalPages}`;
    }
    
    // Actualizar estado de los botones
    const prevButton = document.querySelector('.table-pagination button:first-child');
    const nextButton = document.querySelector('.table-pagination button:last-child');
    if (prevButton) prevButton.disabled = (currentPage === 1);
    if (nextButton) nextButton.disabled = (currentPage === totalPages);
}

// Función para cambiar el número de entradas por página
function changeEntries() {
    rowsPerPage = parseInt(document.getElementById('entries').value);
    currentPage = 1; // Resetear a la primera página
    displayPage(currentPage);
}

// Función para ir a la página siguiente
function nextPage() {
    const vehicleTable = document.querySelector("#vehicle-table tbody");
    const totalRows = vehicleTable.rows.length;

    if (currentPage * rowsPerPage < totalRows) {
        currentPage++;
        displayPage(currentPage);
    }
}

// Función para ir a la página anterior
function previousPage() {
    if (currentPage > 1) {
        displayPage(currentPage - 1);
    }
}

// Función para buscar en la tabla
function searchTable() {
    const input = document.getElementById('search-input').value.toLowerCase();
    const vehicleTable = document.querySelector("#vehicle-table tbody");
    const rows = vehicleTable.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let match = false;

        for (let j = 0; j < cells.length; j++) {
            if (cells[j].innerText.toLowerCase().includes(input)) {
                match = true;
                break;
            }
        }

        rows[i].style.display = match ? '' : 'none';
    }

    // Resetear la paginación cuando se realiza una búsqueda
    currentPage = 1;
    displayPage(currentPage);
}

function imprimirInformacion(id) {
    // Aquí puedes implementar la lógica para imprimir la información
    // Por ejemplo, podrías abrir una nueva ventana con los detalles del vehículo
    window.open(`imprimir_vehiculo.php?id=${id}`, '_blank');
}