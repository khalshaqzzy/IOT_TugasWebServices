<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sensor PIR</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/date-fns"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #2c3e50;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            margin: 0;
            padding: 0;
            color: var(--text-color);
            line-height: 1.6;
        }

        .dashboard {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.8;
            margin-top: 0;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 1.5rem;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 50px;
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
        }

        .btn-secondary:hover {
            background-color: #27ae60;
        }

        .status-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .status-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            flex: 1;
            text-align: center;
            margin: 0 0.5rem;
        }

        .status-card i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .status-card.active i {
            color: var(--secondary-color);
        }

        .status-card h3 {
            margin-bottom: 0.5rem;
        }

        .status-card p {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        .data-table th, .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .data-table th {
            background-color: rgba(52, 152, 219, 0.1);
            font-weight: 600;
        }

        .data-table tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .footer {
            text-align: center;
            margin-top: 3rem;
            padding: 1.5rem 0;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .status-indicator {
                flex-direction: column;
            }
            
            .status-card {
                margin: 0.5rem 0;
            }
            
            .chart-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <h1>Dashboard Monitoring Sensor PIR</h1>
            <p>Visualisasi data real-time gerakan yang terdeteksi</p>
        </div>

        <div class="status-indicator">
            <div class="status-card" id="lastDetection">
                <i class="fas fa-clock"></i>
                <h3>Deteksi Terakhir</h3>
                <p id="lastDetectionTime">Memuat...</p>
            </div>
            <div class="status-card" id="motionStatus">
                <i class="fas fa-walking"></i>
                <h3>Status Gerakan</h3>
                <p id="currentMotionStatus">Tidak Ada</p>
            </div>
            <div class="status-card">
                <i class="fas fa-chart-line"></i>
                <h3>Total Deteksi</h3>
                <p id="totalDetections">0</p>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-chart-area"></i> Grafik Aktivitas Gerakan</h2>
            <div class="chart-container">
                <canvas id="sensorChart"></canvas>
            </div>
            <div class="controls">
                <button class="btn" id="zoomIn">
                    <i class="fas fa-search-plus"></i> Perbesar
                </button>
                <button class="btn" id="zoomOut">
                    <i class="fas fa-search-minus"></i> Perkecil
                </button>
                <button class="btn btn-secondary" id="refreshData">
                    <i class="fas fa-sync-alt"></i> Segarkan Data
                </button>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-table"></i> Data Terbaru</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        <tr>
                            <td colspan="3" style="text-align: center;">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>© 2025 Dashboard Monitoring Sensor PIR | Data diperbarui setiap 5 detik</p>
        </div>
    </div>

    <script>
        // Global variables
        let sensorData = [];
        let zoomLevel = 20;  // Initial number of data points to display
        let isAutoRefresh = true;
        let refreshInterval;

        // Initialize the chart
        const ctx = document.getElementById('sensorChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Gerakan Terdeteksi',
                    data: [],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y === 1 ? 'Gerakan Terdeteksi' : 'Tidak Ada Gerakan';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: { 
                            unit: 'second',
                            displayFormats: {
                                second: 'HH:mm:ss'
                            }
                        },
                        title: { 
                            display: true, 
                            text: 'Waktu',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 1,
                        title: { 
                            display: true, 
                            text: 'Status Gerakan',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            callback: function(value) {
                                return value === 0 ? 'Tidak Ada' : 'Terdeteksi';
                            },
                            stepSize: 1
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                },
                animation: {
                    duration: 1000
                }
            }
        });

        // Format date for display
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString() + ', ' + date.toLocaleDateString();
        }

        // Update dashboard elements
        function updateDashboard(data) {
            if (!data || data.length === 0) return;
            
            // Update last detection time
            const lastEntry = data[data.length - 1];
            document.getElementById('lastDetectionTime').textContent = formatDate(lastEntry.timestamp);
            
            // Update current motion status
            const currentStatus = parseInt(lastEntry.motion_detected);
            const statusElement = document.getElementById('currentMotionStatus');
            const statusCard = document.getElementById('motionStatus');
            
            if (currentStatus === 1) {
                statusElement.textContent = 'Terdeteksi';
                statusCard.classList.add('active');
                statusElement.style.color = 'var(--secondary-color)';
            } else {
                statusElement.textContent = 'Tidak Ada';
                statusCard.classList.remove('active');
                statusElement.style.color = 'var(--text-color)';
            }
            
            // Count total detections
            const totalDetections = data.reduce((total, item) => {
                return total + parseInt(item.motion_detected);
            }, 0);
            document.getElementById('totalDetections').textContent = totalDetections;
            
            // Update table
            const tableBody = document.getElementById('dataTableBody');
            tableBody.innerHTML = '';
            
            // Show the 5 most recent entries
            const recentData = data.slice(-5).reverse();
            recentData.forEach((item, index) => {
                const row = document.createElement('tr');
                
                const numberCell = document.createElement('td');
                numberCell.textContent = index + 1;
                row.appendChild(numberCell);
                
                const timeCell = document.createElement('td');
                timeCell.textContent = formatDate(item.timestamp);
                row.appendChild(timeCell);
                
                const statusCell = document.createElement('td');
                if (parseInt(item.motion_detected) === 1) {
                    statusCell.textContent = 'Gerakan Terdeteksi';
                    statusCell.style.color = 'var(--secondary-color)';
                } else {
                    statusCell.textContent = 'Tidak Ada Gerakan';
                }
                row.appendChild(statusCell);
                
                tableBody.appendChild(row);
            });
        }

        // Fetch data from API
        function fetchData() {
            console.log('Mengambil data dari API...');
            
            // You can use the loading indicators here
            document.getElementById('refreshData').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
            
            fetch('http://192.168.178.165/codeigniter4/public/sensor/data')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data diterima:', data);
                    if (!Array.isArray(data)) {
                        throw new Error('Data bukan array!');
                    }
                    
                    // Store all data and update with zoom level
                    sensorData = data;
                    updateChart();
                    updateDashboard(data);
                    
                    // Reset button text
                    document.getElementById('refreshData').innerHTML = '<i class="fas fa-sync-alt"></i> Segarkan Data';
                })
                .catch(error => {
                    console.error('Gagal mengambil data:', error);
                    document.getElementById('refreshData').innerHTML = '<i class="fas fa-sync-alt"></i> Segarkan Data';
                    alert('Gagal mengambil data: ' + error.message);
                });
        }

        // Update chart based on zoom level
        function updateChart() {
            if (sensorData.length === 0) return;
            
            // Take the most recent data based on zoomLevel
            const recentData = sensorData.slice(-zoomLevel);
            const labels = recentData.map(d => new Date(d.timestamp));
            const motionData = recentData.map(d => parseInt(d.motion_detected));
            
            chart.data.labels = labels;
            chart.data.datasets[0].data = motionData;
            chart.update();
        }

        // Event listeners
        document.getElementById('zoomIn').addEventListener('click', () => {
            if (zoomLevel > 5) {  // Minimum zoom level
                zoomLevel = Math.round(zoomLevel * 0.7);
                updateChart();
            }
        });

        document.getElementById('zoomOut').addEventListener('click', () => {
            if (zoomLevel < sensorData.length) {
                zoomLevel = Math.min(sensorData.length, Math.round(zoomLevel * 1.5));
                updateChart();
            }
        });

        document.getElementById('refreshData').addEventListener('click', fetchData);

        // Auto refresh
        function startAutoRefresh() {
            stopAutoRefresh(); // Clear any existing interval
            refreshInterval = setInterval(fetchData, 5000);  // Update every 5 seconds
        }

        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        }

        // Initialize
        startAutoRefresh();
        fetchData();  // Initial fetch
    </script>
</body>
</html>