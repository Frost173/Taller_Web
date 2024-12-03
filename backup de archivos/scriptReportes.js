// scriptReportes.js
console.log('scriptReportes.js cargado correctamente');
// Variable para almacenar la instancia del gráfico
let chartInstance = null;
document.addEventListener('DOMContentLoaded', function() {
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
        // Agregar manejo de respuesta vacía
        const texto = await response.text();
        if (!texto) {
            throw new Error('La respuesta está vacía');
        }
        // Intentar parsear el JSON después de verificar que hay contenido
        let data;
        try {
            data = JSON.parse(texto);
        } catch (e) {
            console.error('Respuesta del servidor:', texto);
            throw new Error('Error al parsear JSON: ' + e.message);
        }        
        if (data.success) {
            if (!Array.isArray(data.data)) {
                throw new Error('Los datos recibidos no tienen el formato esperado');
            }
            await procesarDatosYActualizar(data.data);
        } else {
            throw new Error(data.error || 'Error desconocido al obtener datos');
        }
    } catch (error) {
        console.error('Error:', error.message);
        // Mostrar el error al usuario
        const chartContainer = document.getElementById('vehicleEntriesChart').parentElement;
        chartContainer.innerHTML = `<div class="alert alert-danger">
            Error al cargar los datos: ${error.message}
        </div>`;
    }
}
async function procesarDatosYActualizar(datos) {
    try {
        console.log('Datos recibidos:', datos); // Para debugging
        const chartData = await procesarDatosParaGrafica(datos);
        console.log('Datos procesados:', chartData); // Para debugging
        crearGrafica(chartData);
        actualizarResumen(datos);
    } catch (error) {
        console.error('Error al procesar datos:', error);
        throw error; // Mantener el stack trace original
    }
}
async function procesarDatosParaGrafica(datos) {
    // Obtener fecha actual
    const hoy = new Date();
    const ultimos10Dias = [];
    // Crear array con los últimos 10 días
    for (let i = 9; i >= 0; i--) {
        const fecha = new Date(hoy);
        fecha.setDate(fecha.getDate() - i);
        const fechaStr = fecha.toISOString().split('T')[0]; // Formato YYYY-MM-DD
        
        ultimos10Dias.push({
            fecha: fechaStr,
            total_entradas: 0
        });
    }
    // Combinar con datos reales
    if (Array.isArray(datos)) {
        datos.forEach(dato => {
            if (dato && dato.fecha) {
                const index = ultimos10Dias.findIndex(dia => dia.fecha === dato.fecha);
                if (index !== -1) {
                    ultimos10Dias[index].total_entradas = parseInt(dato.total_entradas) || 0;
                }
            }
        });
    }
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