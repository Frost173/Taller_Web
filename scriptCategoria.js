let currentTablePage = 1;
let currentDetailsPage = 1;
let allCategories = [];
document.addEventListener("DOMContentLoaded", function() {
    addPaginationControls();
    cargarCategorias();
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle'); /*Este elemento será el 
    botón o enlace que permite abrir o cerrar el menú lateral (sidebar).*/
    const sidebar = document.querySelector('.sidebar'); /*Este elemento representa la barra lateral 
    (sidebar) que queremos mostrar u ocultar al interactuar con el botón.*/

    mobileMenuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    }); /*Al alternar la clase active, la barra lateral (sidebar) se mostrará o se ocultará, dependiendo 
    de cómo esté configurada la clase active en el CSS.*/

    // Close sidebar when clicking outside
    document.addEventListener('click', (event) => {
        if (!sidebar.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    });
    /*Permite cerrar la barra lateral si el usuario hace clic fuera de ella o del botón, proporcionando 
    una experiencia de usuario más intuitiva.*/
});
// Función para agregar controles de paginación
function addPaginationControls() {
    // Selector para la tabla
    const tableControls = document.createElement("div");
    tableControls.className = "pagination-controls mb-3";
    tableControls.innerHTML = `
        <label>Mostrar: 
            <select id="table-page-size" class="form-select">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
            </select>
        </label>
    `;
    document.querySelector(".table-categorias").parentNode.insertBefore(tableControls, document.querySelector(".table-categorias"));

    // Selector y paginación para la lista de detalles
    const detailsControls = document.createElement("div");
    detailsControls.className = "pagination-controls mb-3";
    detailsControls.innerHTML = `
        <label>Mostrar: 
            <select id="details-page-size" class="form-select">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
            </select>
        </label>
        <div class="details-pagination">
            <button class="prev-details">Anterior</button>
            <span class="details-page-info">1</span>
            <button class="next-details">Siguiente</button>
        </div>
    `;
    document.getElementById("details-list").parentNode.insertBefore(detailsControls, document.getElementById("details-list"));

    // Event listeners para los selectores
    document.getElementById("table-page-size").addEventListener("change", function() {
        currentTablePage = 1;
        cargarCategorias();
    });

    document.getElementById("details-page-size").addEventListener("change", function() {
        currentDetailsPage = 1;
        cargarCategorias();
    });

    // Event listeners para la paginación de detalles
    document.querySelector('.prev-details').addEventListener('click', () => {
        if (currentDetailsPage > 1) {
            currentDetailsPage--;
            actualizarDetalles(paginarDatos(allCategories, currentDetailsPage, parseInt(document.getElementById("details-page-size").value)));
            actualizarPaginacionDetalles();
        }
    });

    document.querySelector('.next-details').addEventListener('click', () => {
        const pageSize = parseInt(document.getElementById("details-page-size").value);
        const maxPages = Math.ceil(allCategories.length / pageSize);
        if (currentDetailsPage < maxPages) {
            currentDetailsPage++;
            actualizarDetalles(paginarDatos(allCategories, currentDetailsPage, pageSize));
            actualizarPaginacionDetalles();
        }
    });
}

function paginarDatos(datos, pagina, porPagina) {
    const inicio = (pagina - 1) * porPagina;
    return datos.slice(inicio, inicio + porPagina);
}

// Función para cargar y mostrar categorías
function cargarCategorias() {
    fetch("obtener_categorias.php")
        .then(response => response.json())
        .then(data => {
            console.log('Datos recibidos:', data);
            
            // Normalizar el estado de modo
            allCategories = data.map(categoria => ({
                ...categoria,
                modo: categoria.modo.charAt(0).toUpperCase() + categoria.modo.slice(1).toLowerCase()
            }));
            
            const tablePageSize = parseInt(document.getElementById("table-page-size").value);
            const detailsPageSize = parseInt(document.getElementById("details-page-size").value);
            
            actualizarTabla(paginarDatos(allCategories, currentTablePage, tablePageSize));
            actualizarDetalles(paginarDatos(allCategories, currentDetailsPage, detailsPageSize));
            actualizarPaginacion();
            actualizarPaginacionDetalles();
        })
        .catch(error => {
            console.error('Error al cargar categorías:', error);
            alert('Error al cargar las categorías. Por favor, recarga la página.');
        });
}

// Función para actualizar la tabla
function actualizarTabla(categorias) {
    const tbody = document.querySelector(".table-categorias tbody");
    tbody.innerHTML = "";

    categorias.forEach((categoria) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${categoria.id_tipo || ''}</td>
            <td>${categoria.tipo || ''}</td>
            <td>${categoria.precio || ''}</td>
            <td>
                <input type="checkbox" 
                       ${categoria.modo === "Activado" ? "checked" : ""} 
                       data-id="${categoria.id_tipo}" 
                       class="estado-checkbox"
                       onchange="manejarCambioEstado(this)">
            </td>
            <td class="estado-texto">
                ${categoria.modo || ''}
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Función para actualizar el estado
function actualizarEstado(id, modo, checkbox, estadoTexto) {
    const datos = {
        id: parseInt(id),
        modo: modo
    };

    fetch("actualizar_estado.php", {
        method: "POST",
        headers: { 
            "Content-Type": "application/json",
            "Cache-Control": "no-cache"
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar el estado visual
            estadoTexto.textContent = modo;
            
            // Actualizar el estado en allCategories
            const categoriaIndex = allCategories.findIndex(cat => cat.id_tipo === parseInt(id));
            if (categoriaIndex !== -1) {
                allCategories[categoriaIndex].modo = modo;
            }
            
            // Actualizar los detalles y la tabla
            const tablePageSize = parseInt(document.getElementById("table-page-size").value);
            const detailsPageSize = parseInt(document.getElementById("details-page-size").value);
            
            actualizarTabla(paginarDatos(allCategories, currentTablePage, tablePageSize));
            actualizarDetalles(paginarDatos(allCategories, currentDetailsPage, detailsPageSize));
        } else {
            // Revertir el cambio en caso de error
            checkbox.checked = !checkbox.checked;
            estadoTexto.textContent = checkbox.checked ? "Activado" : "Desactivado";
            alert("Error al actualizar el modo: " + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error("Error en la petición:", error);
        // Revertir el cambio en caso de error
        checkbox.checked = !checkbox.checked;
        estadoTexto.textContent = checkbox.checked ? "Activado" : "Desactivado";
        alert("Error al procesar la solicitud: " + error.message);
    })
    .finally(() => {
        // Habilitar el checkbox una vez completada la operación
        checkbox.disabled = false;
    });
}


// Función para manejar el cambio de estado
function manejarCambioEstado(checkbox) {
    // Deshabilitar el checkbox mientras se procesa
    checkbox.disabled = true;
    
    const estadoTexto = checkbox.parentElement.nextElementSibling;
    const modo = checkbox.checked ? "Activado" : "Desactivado";
    const id = checkbox.getAttribute("data-id");
    
    // Realizar la actualización en el servidor
    actualizarEstado(id, modo, checkbox, estadoTexto);
}
// Función para actualizar los detalles de las categorías
function actualizarDetalles(categorias) {
    const detailsList = document.getElementById("details-list");
    detailsList.innerHTML = "";

    categorias.forEach(categoria => {
        const detailItem = document.createElement("div");
        detailItem.classList.add("detail-item");
        detailItem.innerHTML = `
            <span>${categoria.tipo}</span>
            <span>$${categoria.precio}</span>
        `;
        detailsList.appendChild(detailItem);
    });
}

// Función para actualizar la paginación de la tabla
function actualizarPaginacion() {
    const pageSize = parseInt(document.getElementById("table-page-size").value);
    const maxPages = Math.ceil(allCategories.length / pageSize);
    document.querySelector('.pagination label').textContent = `${currentTablePage} de ${maxPages}`;
    
    // Actualizar estado de los botones
    document.querySelector('.pagination button:first-child').disabled = currentTablePage === 1;
    document.querySelector('.pagination button:last-child').disabled = currentTablePage === maxPages;
}

// Función para actualizar la paginación de los detalles
function actualizarPaginacionDetalles() {
    const pageSize = parseInt(document.getElementById("details-page-size").value);
    const maxPages = Math.ceil(allCategories.length / pageSize);
    document.querySelector('.details-page-info').textContent = `${currentDetailsPage} de ${maxPages}`;
    
    // Actualizar estado de los botones
    document.querySelector('.prev-details').disabled = currentDetailsPage === 1;
    document.querySelector('.next-details').disabled = currentDetailsPage === maxPages;
}

// Mantener el código del formulario existente
document.getElementById("form-categoria").addEventListener("submit", function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch("guardar_categoria.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            this.reset();
            cargarCategorias();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
    });
});
// Modificar la función cargarCategorias para incluir logging
function cargarCategorias() {
    fetch("obtener_categorias.php")
        .then(response => response.json())
        .then(data => {
            console.log('Datos recibidos:', data); // Para debugging
            
            // Normalizar el estado de modo y asegurarse de que todos los campos existan
            allCategories = data.map(categoria => ({
                id_tipo: categoria.id_tipo,
                tipo: categoria.tipo,
                precio: categoria.precio,
                modo: (categoria.modo || 'activado').charAt(0).toUpperCase() + 
                      (categoria.modo || 'activado').slice(1).toLowerCase()
            }));
            
            const tablePageSize = parseInt(document.getElementById("table-page-size").value);
            const detailsPageSize = parseInt(document.getElementById("details-page-size").value);
            
            actualizarTabla(paginarDatos(allCategories, currentTablePage, tablePageSize));
            actualizarDetalles(paginarDatos(allCategories, currentDetailsPage, detailsPageSize));
            actualizarPaginacion();
            actualizarPaginacionDetalles();
        })
        .catch(error => {
            console.error('Error al cargar categorías:', error);
            alert('Error al cargar las categorías. Por favor, recarga la página.');
        });
}
