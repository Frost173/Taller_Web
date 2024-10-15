let currentPage = 1;
let rowsPerPage = 5;

document.addEventListener('DOMContentLoaded', function() {
    actualizarTablaVehiculos();// Llamar a esta función cuando se carga la página
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', addVehicle);
    } else {
        console.error('El formulario no se encontró en el DOM');
    }
    displayPage(currentPage);
});

function actualizarTablaVehiculos() {
    fetch('obtener_vehiculos.php')
    .then(response => response.json())
    .then(data => {
        const vehicleTable = document.querySelector("#vehicle-table tbody");
        vehicleTable.innerHTML = '';
        if (data.length === 0) {
            vehicleTable.innerHTML = '<tr><td colspan="6">No hay datos disponibles en la tabla</td></tr>';
        } else {
            data.forEach((vehiculo, index) => {
                const row = vehicleTable.insertRow();
                row.innerHTML = `
                    <td>${vehiculo.id}</td>
                    <td>${vehiculo.placa}</td>
                    <td>${vehiculo.estacionamiento}</td>
                    <td>${vehiculo.fecha_hora}</td>
                    <td><span class="vehicle-status">${vehiculo.estado}</span></td>
                    <td><button onclick="imprimirInformacion(${vehiculo.id})">Imprimir</button></td>
                `;
            });
        }
        displayPage(1);
    })
    .catch((error) => {
        console.error('Error al obtener vehículos:', error);
    });
}   

function addVehicle(event) {
    event.preventDefault();
    console.log("addVehicle function called");

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

    console.log("Sending data:", JSON.stringify(vehicleData));

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
        console.log("Respuesta del servidor:", data);
        if (data.success) {
            console.log("Vehículo agregado con éxito");
            alert("Vehículo agregado correctamente");
            actualizarTablaVehiculos();
            document.getElementById('addVehicleForm').reset();
        } else {
            console.error('Error al agregar vehículo:', data.error);
            alert("Error al agregar vehículo: " + data.error);
        }
    })
    .catch((error) => {
        console.error('Error en fetch:', error);
        alert("Error de conexión al agregar vehículo");
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

function imprimirInformacion(id) {
    // Aquí puedes implementar la lógica para imprimir la información
    // Por ejemplo, podrías abrir una nueva ventana con los detalles del vehículo
    window.open(`imprimir_vehiculo.php?id=${id}`, '_blank');
}
