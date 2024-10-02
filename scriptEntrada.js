let currentPage = 1;
let rowsPerPage = 5;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', addVehicle);
    } else {
        console.error('El formulario no se encontró en el DOM');
    }
    displayPage(currentPage);
});

function addVehicle(event) {
    event.preventDefault();

    const placa = document.getElementById('placa').value;
    const tipo = document.getElementById('tipo').value;
    const estacionamiento = document.getElementById('numero-estacionamiento').value;
    const precio = document.getElementById('precio').value;
    const fechaHora = new Date().toISOString().slice(0, 19).replace('T', ' ');
    const estado = "Estacionado";

    // Crear objeto con los datos del vehículo
    const vehicleData = {
        placa,
        tipo,
        estacionamiento,
        precio,
        fechaHora,
        estado
    };

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
            // Actualizar la tabla en el frontend
            actualizarTablaVehiculos();
        } else {
            console.error('Error al agregar vehículo:', data.error);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });

    // Limpiar el formulario
    document.getElementById('placa').value = '';
    document.getElementById('tipo').value = 'Seleccione-tipo';
    document.getElementById('numero-estacionamiento').value = 'Seleccione-tipo';
    document.getElementById('precio').value = 'Seleccione-precio';
}

function actualizarTablaVehiculos() {
    fetch('obtener_vehiculos.php')
    .then(response => response.json())
    .then(data => {
        // Actualizar la tabla con los datos recibidos
        const vehicleTable = document.querySelector("#vehicle-table tbody");
        vehicleTable.innerHTML = '';
        data.forEach((vehiculo, index) => {
            const row = vehicleTable.insertRow();
            row.insertCell(0).innerText = index + 1;
            row.insertCell(1).innerText = vehiculo.placa;
            row.insertCell(2).innerText = vehiculo.estacionamiento;
            row.insertCell(3).innerText = vehiculo.fecha_hora;
            row.insertCell(4).innerHTML = `<span class="vehicle-status">${vehiculo.estado}</span>`;
            // Agregar botón de imprimir aquí si es necesario
        });
        displayPage(1);
    })
    .catch((error) => {
        console.error('Error:', error);
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
        if (index >= start && index < end) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Actualiza el estado de la paginación
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    const paginationSpan = document.querySelector('.table-pagination span');
    if (paginationSpan) {
        paginationSpan.innerText = `Página ${currentPage} de ${totalPages}`;
    }
    
    // Habilitar/deshabilitar botones de paginación según corresponda
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

// Asegurarse de que displayPage se llame cuando la página se carga
document.addEventListener('DOMContentLoaded', function() {
    displayPage(currentPage);
});

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
        currentPage--;
        displayPage(currentPage);
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
}
