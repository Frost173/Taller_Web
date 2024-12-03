const style = document.createElement('style');
style.textContent = `
    .estado-ocupado {
        background-color: #E3F2FD;
        padding: 5px 10px;
        border-radius: 4px;
        display: inline-block;
    }
    .estado-estacionado {
        background-color: #E8F5E9;
        color: #2E7D32;
        padding: 5px 10px;
        border-radius: 4px;
        display: inline-block;
    }
    .btn-imprimir {
        background-color: #2196F3;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .btn-imprimir:hover {
        background-color: #1976D2;
    }
    .btn-imprimir:disabled {
        background-color: #BDBDBD;
        cursor: not-allowed;
    }
    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px;
        border-radius: 4px;
        z-index: 1000;
        max-width: 400px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .alert-info {
        background-color: #E3F2FD;
        color: #1565C0;
        border: 1px solid #90CAF9;
    }
    .alert-error {
        background-color: #FFEBEE;
        color: #C62828;
        border: 1px solid #FFCDD2;
    }
    /* Resto de tus estilos existentes */
`;
document.head.appendChild(style);

let currentPage = 1;
let totalPages = 1;

document.addEventListener('DOMContentLoaded', function() {
    cargarTipos();
    cargarVehiculos();
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

    // Evento para cambio en número de entradas
    document.getElementById('entries').addEventListener('change', function() {
        currentPage = 1;
        cargarVehiculos();
    });

    // Evento para búsqueda con debounce
    document.getElementById('search-input').addEventListener('input', debounce(function() {
        currentPage = 1;
        cargarVehiculos();
    }, 300));
});

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}


function cargarTipos() {
    fetch('getTipos.php')
        .then(response => response.json())
        .then(tipos => {
            const tipoSelect = document.getElementById('tipo');
            const precioSelect = document.getElementById('precio');
            
            tipoSelect.innerHTML = '<option value="">Seleccione Tipo</option>';
            precioSelect.innerHTML = '<option value="">Seleccione precio</option>';
            
            tipos.forEach(tipo => {
                tipoSelect.innerHTML += `<option value="${tipo.id_tipo}">${tipo.tipo}</option>`;
                precioSelect.innerHTML += `<option value="${tipo.id_tipo}">$${tipo.precio} → (${tipo.tipo})</option>`;
            });
        })
        .catch(error => console.error('Error:', error));
}


function cargarVehiculos() {
    const entriesPerPage = document.getElementById('entries').value;
    const searchTerm = document.getElementById('search-input').value.trim();
    
    fetch(`getVehiculos.php?limit=${entriesPerPage}&page=${currentPage}&search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }

            const tbody = document.querySelector('#vehicle-table tbody');
            totalPages = data.pages;

            if (data.vehiculos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay vehículos registrados</td></tr>';
                document.querySelector('.table-pagination').style.display = 'none';
                return;
            }

            // Actualizar tabla
            tbody.innerHTML = data.vehiculos.map((vehiculo, index) => {
                const estadoClass = `estado estado-${vehiculo.estado.toLowerCase()}`;
                const isDisabled = vehiculo.estado === 'estacionado' ? 'disabled' : '';
                
                return `
                    <tr>
                        <td>${((currentPage - 1) * entriesPerPage) + index + 1}</td>
                        <td>${vehiculo.placa}</td>
                        <td>${vehiculo.fecha_entrada}</td>
                        <td>${vehiculo.hora_entrada}</td>
                        <td><span class="${estadoClass}">${vehiculo.estado}</span></td>
                        <td>
                            <button onclick="imprimirTicket(${vehiculo.id_estacionamiento})" 
                                    class="btn-imprimir"
                                    ${isDisabled}>
                                Imprimir
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            actualizarPaginacion(data.pages);
            document.querySelector('.table-pagination').style.display = data.pages > 1 ? 'flex' : 'none';
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            alert('Error al cargar los vehículos. Por favor, intente nuevamente.');
        });
}

function actualizarPaginacion(totalPages) {
    const paginationContainer = document.querySelector('.table-pagination');
    
    if (totalPages <= 1) {
        paginationContainer.style.display = 'none';
        return;
    }

    paginationContainer.innerHTML = `
        <button onclick="cambiarPagina(1)" class="first" ${currentPage === 1 ? 'disabled' : ''}>
            Primera
        </button>
        <button onclick="cambiarPagina(${currentPage - 1})" class="prev" ${currentPage === 1 ? 'disabled' : ''}>
            Anterior
        </button>
        <span class="page-info">Página ${currentPage} de ${totalPages}</span>
        <button onclick="cambiarPagina(${currentPage + 1})" class="next" ${currentPage === totalPages ? 'disabled' : ''}>
            Siguiente
        </button>
        <button onclick="cambiarPagina(${totalPages})" class="last" ${currentPage === totalPages ? 'disabled' : ''}>
            Última
        </button>
    `;

    paginationContainer.style.display = 'flex';
}

function cambiarPagina(newPage) {
    if (newPage > 0 && newPage <= totalPages && newPage !== currentPage) {
        currentPage = newPage;
        cargarVehiculos();
    }
}

function addVehicle(event) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append('placa', document.getElementById('placa').value);
    formData.append('tipo', document.getElementById('tipo').value);

    fetch('addVehiculo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById('addVehicleForm').reset();
            cargarVehiculos();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function imprimirTicket(id) {
    window.open(`imprimirVehiculo.php?id=${id}`, 'TicketEstacionamiento', 
        'width=400,height=600,resizable=yes,scrollbars=yes');
}

function mostrarError(mensaje) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-error';
    errorDiv.textContent = mensaje;
    document.body.appendChild(errorDiv);
    
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

async function imprimirTicket(idEstacionamiento) {
    try {
        console.log('Iniciando impresión para ID:', idEstacionamiento);
        
        // Obtener el ticket directamente
        const response = await fetch(`imprimir_ticket.php?id=${idEstacionamiento}`, {
            method: 'GET',
            headers: {
                'Accept': 'text/html',
                'Cache-Control': 'no-cache'
            },
        });

        if (!response.ok) {
            throw new Error(`Error HTTP ${response.status}`);
        }

        // Obtener el HTML del ticket directamente
        const ticketHTML = await response.text();
        
        // Abrir ventana de impresión
        const ventanaImpresion = window.open('', '_blank', 'width=400,height=600');
        if (!ventanaImpresion) {
            throw new Error('El navegador bloqueó la ventana emergente. Por favor, permita las ventanas emergentes para este sitio.');
        }
        
        // Escribir el contenido HTML en la ventana
        ventanaImpresion.document.open();
        ventanaImpresion.document.write(ticketHTML);
        ventanaImpresion.document.close();
        
        // Esperar a que la ventana cargue y luego imprimir
        ventanaImpresion.onload = () => {
            setTimeout(() => {
                ventanaImpresion.print();
                ventanaImpresion.onafterprint = () => {
                    ventanaImpresion.close();
                };
            }, 500);
        };
        
    } catch (error) {
        console.error('Error en imprimirTicket:', error);
        alert(`Error al generar el ticket: ${error.message}`);
    }
}