<?php
include_once '../componentes/header.php';
?>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
      <?php include_once '../componentes/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content" class="p-4 bg-gray-100 flex-grow">
           
        <?php
           include_once '../componentes/barra_nav.php';
        ?>

            <!-- Resumen de Métricas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="card card-custom p-4 bg-white">
                    <div class="flex items-center">
                        <i class="bi bi-person text-blue-500 text-3xl me-3"></i>
                        <div>
                            <h5 class="text-lg font-semibold text-gray-500">Pacientes Totales</h5>
                            <p class="text-2xl font-bold" id="total-pacientes"></p>
                        </div>
                    </div>
                </div>
                <div class="card card-custom p-4 bg-white">
                    <div class="flex items-center">
                        <i class="bi bi-people-fill text-green-500 text-3xl me-3"></i>
                        <div>
                            <h5 class="text-lg font-semibold text-gray-500">Personal Médico</h5>
                            <p class="text-2xl font-bold" id="total-personal"></p>
                        </div>
                    </div>
                </div>
                <div class="card card-custom p-4 bg-white">
                    <div class="flex items-center">
                        <i class="bi bi-file-earmark-medical-fill text-yellow-500 text-3xl me-3"></i>
                        <div>
                            <h5 class="text-lg font-semibold text-gray-500">Consultas Recientes</h5>
                            <p class="text-2xl font-bold" id="total-consultas"></p>
                        </div>
                    </div>
                </div>
                <div class="card card-custom p-4 bg-white">
                    <div class="flex items-center">
                        <i class="bi bi-clipboard-data-fill text-red-500 text-3xl me-3"></i>
                        <div>
                            <h5 class="text-lg font-semibold text-gray-500">Hospitales Activos</h5>
                            <p class="text-2xl font-bold" id="total-hospitales">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos de Reporte -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <div class="card card-custom p-4 bg-white">
                    <h5 class="text-lg font-semibold mb-3">Pacientes por Nacionalidad</h5>
                    <div class="chart-container">
                        <canvas id="nacionalidadChart"></canvas>
                    </div>
                </div>
                <div class="card card-custom p-4 bg-white">
                    <h5 class="text-lg font-semibold mb-3">Ingresos vs Gastos (Anual)</h5>
                    <div class="chart-container">
                        <canvas id="finanzasChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabla de Reportes -->
            <div class="card card-custom p-4 bg-white">
                <h5 class="text-lg font-semibold mb-3">Últimas Consultas</h5>
                <div class="table-container">
                    <table class="table table-striped table-hover rounded-xl">
                        <thead>
                            <tr>
                                <th scope="col">ID Consulta</th>
                                <th scope="col">Paciente</th>
                                <th scope="col">Médico</th>
                                <th scope="col">Hospital</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Diagnóstico</th>
                                <th scope="col">Pagado</th>
                            </tr>
                        </thead>
                        <tbody id="consultas-table-body">
                            <!-- Datos de consultas se insertarán aquí con JS -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
       
        // Datos de muestra basados en el esquema de la base de datos

        const mockData = {
            pacientes: [
                { id: 1, nombre: 'Juan', apellido: 'Perez', nacionalidad: 'Guineano' },
                { id: 2, nombre: 'Maria', apellido: 'Gomez', nacionalidad: 'Española' },
                { id: 3, nombre: 'Aliou', apellido: 'Diallo', nacionalidad: 'Senegalés' },
                { id: 4, nombre: 'Ana', apellido: 'Lopez', nacionalidad: 'Guineana' },
                { id: 5, nombre: 'Peter', apellido: 'Jones', nacionalidad: 'Estadounidense' },
                { id: 6, nombre: 'Fatou', apellido: 'Sow', nacionalidad: 'Senegalés' }
            ],
            personal: [
                { id: 101, nombre: 'Dr. Carlos', apellido: 'Mora', especialidad: 'Cardiología' },
                { id: 102, nombre: 'Enf. Lucia', apellido: 'Flores', especialidad: 'Enfermería' },
                { id: 103, nombre: 'Dr. Javier', apellido: 'Sanz', especialidad: 'Pediatría' }
            ],
            hospitales: [
                { id: 1, nombre: 'Hospital Central de Malabo', distrito: 'Malabo' },
                { id: 2, nombre: 'Hospital de Bata', distrito: 'Bata' }
            ],
            consultas: [
                { id: 201, paciente: 'Juan Perez', medico: 'Dr. Carlos Mora', hospital: 'Hospital Central de Malabo', fecha: '2024-05-15', diagnostico: 'Resfriado común', pagado: true },
                { id: 202, paciente: 'Maria Gomez', medico: 'Dr. Javier Sanz', hospital: 'Hospital de Bata', fecha: '2024-05-14', diagnostico: 'Fractura de muñeca', pagado: false },
                { id: 203, paciente: 'Aliou Diallo', medico: 'Dr. Carlos Mora', hospital: 'Hospital Central de Malabo', fecha: '2024-05-13', diagnostico: 'Fiebre alta', pagado: true },
                { id: 204, paciente: 'Ana Lopez', medico: 'Dr. Carlos Mora', hospital: 'Hospital de Bata', fecha: '2024-05-13', diagnostico: 'Dolor de garganta', pagado: true },
                { id: 205, paciente: 'Peter Jones', medico: 'Dr. Javier Sanz', hospital: 'Hospital Central de Malabo', fecha: '2024-05-12', diagnostico: 'Revisión anual', pagado: false }
            ],
            finanzas: {
                ingresos: [150000, 180000, 165000, 200000, 190000, 220000],
                gastos: [100000, 120000, 110000, 130000, 125000, 150000],
                meses: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun']
            }
        };

       
        // Llenar métricas y tablas con datos de muestra
    

        document.getElementById('total-pacientes').textContent = mockData.pacientes.length;
        document.getElementById('total-personal').textContent = mockData.personal.length;
        document.getElementById('total-consultas').textContent = mockData.consultas.length;
        document.getElementById('total-hospitales').textContent = mockData.hospitales.length;

        const consultasTableBody = document.getElementById('consultas-table-body');
        mockData.consultas.forEach(consulta => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${consulta.id}</td>
                <td>${consulta.paciente}</td>
                <td>${consulta.medico}</td>
                <td>${consulta.hospital}</td>
                <td>${consulta.fecha}</td>
                <td>${consulta.diagnostico}</td>
                <td>
                    <span class="badge ${consulta.pagado ? 'bg-success' : 'bg-danger'}">
                        ${consulta.pagado ? 'Sí' : 'No'}
                    </span>
                </td>
            `;
            consultasTableBody.appendChild(row);
        });

       
        // Configuración y renderizado de gráficos con Chart.js
        

        // Gráfico de Pacientes por Nacionalidad
        const nacionalidadCounts = mockData.pacientes.reduce((acc, paciente) => {
            acc[paciente.nacionalidad] = (acc[paciente.nacionalidad] || 0) + 1;
            return acc;
        }, {});
        const nacionalidadLabels = Object.keys(nacionalidadCounts);
        const nacionalidadData = Object.values(nacionalidadCounts);
        
        const nacionalidadCtx = document.getElementById('nacionalidadChart').getContext('2d');
        const nacionalidadChart = new Chart(nacionalidadCtx, {
            type: 'bar',
            data: {
                labels: nacionalidadLabels,
                datasets: [{
                    label: 'Número de Pacientes',
                    data: nacionalidadData,
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.8)',
                        'rgba(46, 204, 113, 0.8)',
                        'rgba(241, 196, 15, 0.8)',
                        'rgba(231, 76, 60, 0.8)',
                        'rgba(155, 89, 182, 0.8)'
                    ],
                    borderColor: [
                        'rgba(52, 152, 219, 1)',
                        'rgba(46, 204, 113, 1)',
                        'rgba(241, 196, 15, 1)',
                        'rgba(231, 76, 60, 1)',
                        'rgba(155, 89, 182, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Finanzas (Ingresos vs Gastos)
        const finanzasCtx = document.getElementById('finanzasChart').getContext('2d');
        const finanzasChart = new Chart(finanzasCtx, {
            type: 'line',
            data: {
                labels: mockData.finanzas.meses,
                datasets: [{
                    label: 'Ingresos',
                    data: mockData.finanzas.ingresos,
                    borderColor: 'rgba(46, 204, 113, 1)',
                    backgroundColor: 'rgba(46, 204, 113, 0.2)',
                    fill: true,
                    tension: 0.1
                }, {
                    label: 'Gastos',
                    data: mockData.finanzas.gastos,
                    borderColor: 'rgba(231, 76, 60, 1)',
                    backgroundColor: 'rgba(231, 76, 60, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
