<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerability Scanner</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Wrapper to center everything -->
    <div class="center-wrapper">
        <!-- Header Section -->
        <header>
            <h1>Vulnerability Scanner</h1>
            <p>Scan your network for open ports and potential vulnerabilities.</p>
        </header>

        <!-- Main Content -->
        <div class="scanner-container">
            <button id="start-scan">Start Scan</button>
            <div id="loading">
                <div class="spinner"></div>
                <p>Scanning...</p>
            </div>

            <h2>Scan Results:</h2>
            <div id="scan-results">
                <p>Results will appear here</p>
            </div>
            <div id="error-message" class="error-message"></div>
        </div>
    </div>

    <script>
        document.getElementById('start-scan').addEventListener('click', () => {
            startScan();
        });

        function startScan() {
            const loadingElement = document.getElementById('loading');
            loadingElement.style.display = 'flex';

            const resultsContainer = document.getElementById('scan-results');
            resultsContainer.innerHTML = '<p>Results will appear here</p>';
            const errorMessage = document.getElementById('error-message');
            errorMessage.textContent = '';

            fetch('http://localhost:8080/scan')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Network response was not ok. Status Code: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    loadingElement.style.display = 'none';
                    displayScanResults(data);
                })
                .catch(error => {
                    loadingElement.style.display = 'none';
                    console.error('Fetch error:', error.message);
                    errorMessage.textContent = `Error occurred: ${error.message}`;
                });
        }

        function displayScanResults(data) {
            const resultsContainer = document.getElementById('scan-results');
            resultsContainer.innerHTML = '';

            for (const [host, hostData] of Object.entries(data)) {
                const hostInfo = document.createElement('div');
                hostInfo.classList.add('result-card');
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
