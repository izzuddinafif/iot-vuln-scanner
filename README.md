# IoT Vulnerability Scanner

This project is an IoT Vulnerability Scanner that uses Python for network scanning (leveraging Nmap) and Go for the HTTP server to handle scan requests. The setup is ideal for scanning a network to detect open ports and services on IoT devices, making it easier to identify potential security vulnerabilities.

## Project Structure
```
IOT-VULN-SCANNER/
├── go/
│   ├── go.mod                # Go module file for dependencies
│   └── app/
│       └── server.go         # Go HTTP server for handling scan requests
├── python/
│   ├── requirements.txt      # Python dependencies
│   └── scanner_service.py    # Python script that performs the scan using Nmap
├── run.sh                    # Script to start both Go and Python services
├── README.md                 # Project documentation
└── .gitignore                # Ignored files for Git
```

## Requirements
- Go: Version 1.16 or later
- Python: Version 3.7 or later
- Nmap: Ensure Nmap is installed on the system for network scanning

```bash
sudo apt install nmap
``` 

## Setup Instructions

1. Clone the Repository:

```bash
git clone https://github.com/izzuddinafif/iot-vuln-scanner.git
cd iot-vuln-scanner
```

2. Install Python Dependencies: Navigate to the python directory and install the required packages using requirements.txt.

```bash
pip install -r python/requirements.txt
```

3. Run the Project: Use the run.sh script to start both the Go and Python services.

```bash
chmod +x run.sh
./run.sh [optional IP range]
```

- If no IP range is specified, it will default to 192.168.1.0/24.

- Example:

```bash
./run.sh 192.168.217.0/24
```

## Usage

Once the services are running, you can use curl or a similar tool to make HTTP requests to the Go server to initiate scans.

- Initiate a Scan with Default IP Range:
```bash
curl "http://localhost:8080/scan"
```
- Specify a Target IP Range:
```bash
curl "http://localhost:8080/scan?target=192.168.1.0/24"
```
The Go server forwards the scan request to the Python service, which uses Nmap to scan the specified range and returns the results in JSON format.

## Example JSON Response

```json
{
    "192.168.217.165": {
        "addresses": {
            "ipv4": "192.168.217.165"
        },
        "hostnames": [],
        "ports": [
            {
                "port": 53,
                "service": "domain",
                "state": "open"
            }
        ]
    },
    "192.168.217.32": {
        "addresses": {
            "ipv4": "192.168.217.32"
        },
        "hostnames": [],
        "ports": [
            {
                "port": 21,
                "service": "ftp",
                "state": "open"
            },
            {
                "port": 80,
                "service": "http",
                "state": "open"
            }
        ]
    }
}
```
## Customization

- Change Default IP Range: Modify the target flag in server.go to set a different default IP range for scanning.
- Run on Different Ports: Edit server.go and scanner_service.py to configure the HTTP servers to listen on different ports.
- Additional Scanning Options: Modify the scanner_service.py code to add more Nmap options or use Nmap’s scripting engine for specific vulnerability checks.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.
Contributing

Feel free to submit issues or pull requests to improve the project. Contributions are welcome!