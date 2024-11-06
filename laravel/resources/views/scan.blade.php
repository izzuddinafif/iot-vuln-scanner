<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerability Scanner</title>
    <style>
        #loading {
            display: none;
            font-weight: bold;
            color: blue;
        }
        #scan-results div {
            margin-top: 10px;
            padding: 5px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>Vulnerability Scanner</h1>
    <button id="start-scan">Mulai Scan</button>
    <p id="loading">Scanning, please wait...</p>

    <h2>Hasil Scan:</h2>
    <div id="scan-results">
        <p>Hasil scan akan muncul di sini</p>
    </div>

    <script>
        document.getElementById('start-scan').addEventListener('click', () => {
            startScan();
        });

        function startScan() {
            // Tampilkan indikator loading
            const loadingElement = document.getElementById('loading');
            loadingElement.style.display = 'block';
            
            // Bersihkan hasil sebelumnya
            const resultsContainer = document.getElementById('scan-results');
            resultsContainer.innerHTML = '';

            // Kirim permintaan scan ke backend
            fetch('http://localhost:8080/scan')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Network response was not ok. Status Code: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Sembunyikan indikator loading
                    loadingElement.style.display = 'none';

                    // Tampilkan hasil scan
                    displayScanResults(data);
                })
                .catch(error => {
                    loadingElement.style.display = 'none';
                    console.error('Fetch error:', error.message);
                    alert(`Terjadi kesalahan: ${error.message}`);
                });
        }

        function displayScanResults(data) {
            const resultsContainer = document.getElementById('scan-results');
            resultsContainer.innerHTML = ''; // Bersihkan konten sebelumnya

            for (const [host, hostData] of Object.entries(data)) {
                const hostInfo = document.createElement('div');
                hostInfo.innerHTML = `<strong>IP Address:</strong> ${host}`;
                
                const portsList = document.createElement('ul');
                hostData.ports.forEach(portInfo => {
                    const portItem = document.createElement('li');
                    portItem.textContent = `Port: ${portInfo.port}, Service: ${portInfo.service}, State: ${portInfo.state}`;
                    portsList.appendChild(portItem);
                });

                hostInfo.appendChild(portsList);
                resultsContainer.appendChild(hostInfo);
            }
        }
    </script>
</body>
</html>
