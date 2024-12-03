// scriptReportes.js
console.log('scriptReportes.js cargado correctamente');
// Variable para almacenar la instancia del gráfico
let chartInstance = null;
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

    if (typeof Chart === 'undefined') {
        console.error('Chart.js no está cargado. Por favor, incluye la librería.');
        return;
    }
    inicializarReportes();
});
async function inicializarReportes() {
    try {
        const response = await fetch('obtener_datos_reportes.php');
        if (!response.ok) {
            throw new Error(`¡Error HTTP! estado: ${response.status}`);
        }
        const data = await response.json();
        if (data.success) {
            await procesarDatosYActualizar(data.data);
        } else {
            throw new Error(data.error || 'Error desconocido al obtener datos');
        }
    } catch (error) {
        console.error('Error:', error.message);
        mostrarErrorEnContenedor(error.message);
    }
}
function mostrarErrorEnContenedor(mensaje) {
    const chartContainer = document.getElementById('vehicleEntriesChart').parentElement;
    chartContainer.innerHTML = `<div class="alert alert-danger">
        Error al cargar los datos: ${mensaje}
    </div>`;
}
async function procesarDatosYActualizar(datos) {
    try {
        const chartData = await procesarDatosParaGrafica(datos);
        crearGrafica(chartData);
        actualizarResumen(datos);
    } catch (error) {
        console.error('Error al procesar datos:', error);
        throw error;
    }
}
async function procesarDatosParaGrafica(datos) {
    const hoy = new Date();
    const ultimos10Dias = [];

    for (let i = 9; i >= 0; i--) {
        const fecha = new Date(hoy.getTime() - (i * 24 * 60 * 60 * 1000)); // Restar días
        const fechaStr = fecha.toISOString().split('T')[0];
        ultimos10Dias.push({
            fecha: fechaStr,
            total_entradas: 0
        });
    }

    datos.forEach(dato => {
        const index = ultimos10Dias.findIndex(dia => dia.fecha === dato.fecha);
        if (index !== -1) {
            ultimos10Dias[index].total_entradas = parseInt(dato.total_entradas) || 0;
        }
    });

    return ultimos10Dias;
}

function crearGrafica(datos) {
    const canvas = document.getElementById('vehicleEntriesChart');
    if (!canvas) {
        console.error('No se encontró el elemento canvas');
        return;
    }

    const ctx = canvas.getContext('2d');

    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: datos.map(d => formatearFecha(d.fecha)),
            datasets: [{
                label: 'Entradas de Vehículos',
                data: datos.map(d => d.total_entradas),
                fill: true,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}
function formatearFecha(fecha) {
    try {
        const fechaObj = new Date(fecha);
        return fechaObj.toLocaleDateString('es-PE', {
            day: 'numeric',
            month: 'short'
        });
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return fecha; // Devolver la fecha original si hay error
    }
}
function actualizarResumen(datos) {
    try {
        if (!Array.isArray(datos) || datos.length === 0) {
            throw new Error('No hay datos para procesar');
        }

        const totalEntries = datos.reduce((sum, day) => {
            return sum + (parseInt(day.total_entradas) || 0);
        }, 0);

        const dailyAverage = (totalEntries / datos.length).toFixed(1);
        
        const busiestDay = datos.reduce((max, day) => {
            const currentEntries = parseInt(day.total_entradas) || 0;
            const maxEntries = parseInt(max.total_entradas) || 0;
            return currentEntries > maxEntries ? day : max;
        }, datos[0]);

        document.getElementById('total-entries').textContent = totalEntries;
        document.getElementById('daily-average').textContent = dailyAverage;
        
        if (busiestDay && busiestDay.fecha) {
            document.getElementById('busiest-day').textContent = 
                `${formatearFecha(busiestDay.fecha)} (${busiestDay.total_entradas})`;
        }
    } catch (error) {
        console.error('Error al actualizar resumen:', error);
        // No lanzar el error para evitar que se rompa toda la página
    }
}