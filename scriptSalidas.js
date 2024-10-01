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
    event.preventDefault(); // Evitar que el formulario se envíe

    console.log("Función addVehicle llamada"); // Mensaje de depuración

    const placa = document.getElementById('placa').value;
    const tipo = document.getElementById('tipo').value;
    const estacionamiento = document.getElementById('numero-estacionamiento').value;
    const precio = document.getElementById('precio').value;
    const fechaHora = new Date().toLocaleString();
    const estado = "Estacionado";

    console.log("Datos capturados:", { placa, tipo, estacionamiento, precio, fechaHora, estado });

    const vehicleTable = document.querySelector("#vehicle-table tbody");
    if (!vehicleTable) {
        console.error('La tabla de vehículos no se encontró en el DOM');
        return;
    }

    const rowCount = vehicleTable.rows.length;

    if (rowCount === 1 && vehicleTable.rows[0].cells[0].innerText === "No hay datos disponibles en la tabla") {
        vehicleTable.innerHTML = ''; // Limpiar la tabla si solo hay un mensaje de "no hay datos"
    }

    const row = vehicleTable.insertRow();
    row.insertCell(0).innerText = rowCount + 1; // Número de vehículo
    row.insertCell(1).innerText = placa;
    row.insertCell(2).innerText = estacionamiento;
    row.insertCell(3).innerText = fechaHora;
    row.insertCell(4).innerHTML = `<span class="vehicle-status">${estado}</span>`;
    const actionCell = row.insertCell(5);

    const printButton = document.createElement('button');
    printButton.innerText = 'Imprimir';
    printButton.classList.add('action-button');
    printButton.onclick = function() {
        const content = `
            <h1>Información del Vehículo</h1>
            <p><strong>Número de Vehículo:</strong> ${placa}</p>
            <p><strong>Tipo:</strong> ${tipo}</p>
            <p><strong>Número de Área:</strong> ${estacionamiento}</p>
            <p><strong>Precio:</strong> ${precio}</p>
            <p><strong>Hora de Llegada:</strong> ${fechaHora}</p>
            <p><strong>Estado:</strong> ${estado}</p>
        `;

        const newWindow = window.open('', '', 'width=600,height=400');
        newWindow.document.write(content);
        newWindow.document.close();
        newWindow.print();
    };
    actionCell.appendChild(printButton);

    console.log("Vehículo agregado a la tabla");

    // Limpiar los campos del formulario
    document.getElementById('placa').value = '';
    document.getElementById('tipo').value = 'Seleccione-tipo';
    document.getElementById('numero-estacionamiento').value = 'Seleccione-tipo';
    document.getElementById('precio').value = 'Seleccione-precio';

    // Actualiza la paginación después de agregar un nuevo vehículo
    displayPage(currentPage);
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
