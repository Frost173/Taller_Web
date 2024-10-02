document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', addCategory);
    } else {
        console.error('Formulario no encontrado en el DOM');
    }

    displayCategories();  // Cargar categorías previamente guardadas (si fuera el caso)
});

let categories = [];

function addCategory(event) {
    event.preventDefault(); // Prevenir el envío del formulario

    const area = document.getElementById('area').value;
    const vehicleType = document.getElementById('vehicle-type').value;
    const vehicleLimit = document.getElementById('vehicle-limit').value;
    const charge = document.getElementById('charge').value;

    if (!area || !vehicleType || !vehicleLimit || !charge) {
        alert("Por favor, completa todos los campos.");
        return;
    }

    // Agregar nueva categoría al arreglo
    const newCategory = {
        area,
        vehicleType,
        vehicleLimit,
        charge
    };
    
    categories.push(newCategory);

    // Llamar funciones para actualizar la tabla y detalles
    updateCategoryTable();
    updateCategoryDetails();

    // Limpiar el formulario
    form.reset();
}

// Actualiza la tabla de categorías
function updateCategoryTable() {
    const tableBody = document.querySelector('.table-categorias tbody');
    tableBody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos elementos

    categories.forEach((category, index) => {
        const row = tableBody.insertRow();

        row.insertCell(0).innerText = index + 1;  // Número de fila
        row.insertCell(1).innerText = category.area;
        row.insertCell(2).innerText = category.vehicleType;
        row.insertCell(3).innerText = category.vehicleLimit;
        row.insertCell(4).innerText = `$${category.charge}`;
        row.insertCell(5).innerHTML = '<span class="estado activado">Activado</span>';

        const actionCell = row.insertCell(6);
        const deactivateButton = document.createElement('button');
        deactivateButton.classList.add('btn', 'desactivar');
        deactivateButton.textContent = 'Deactivate';
        deactivateButton.onclick = () => deactivateCategory(index);
        actionCell.appendChild(deactivateButton);
    });
}

// Actualiza la lista de detalles de categoría
function updateCategoryDetails() {
    const detailsList = document.querySelector('.details-list');
    detailsList.innerHTML = ''; // Limpiar la lista de detalles

    categories.forEach(category => {
        const detailItem = document.createElement('div');
        detailItem.classList.add('detail-item');
        detailItem.innerHTML = `
            <span>${category.vehicleType}</span>
            <span>$${category.charge}</span>
        `;
        detailsList.appendChild(detailItem);
    });
}

// Función para desactivar una categoría (ejemplo)
function deactivateCategory(index) {
    const category = categories[index];
    alert(`La categoría ${category.vehicleType} ha sido desactivada.`);
}

// Si tienes almacenamiento en localStorage, puedes cargar las categorías guardadas
function displayCategories() {
    if (localStorage.getItem('categories')) {
        categories = JSON.parse(localStorage.getItem('categories'));
        updateCategoryTable();
        updateCategoryDetails();
    }
}
