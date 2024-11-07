# IoT Vulnerability Scanner

This project is an IoT Vulnerability Scanner that uses Laravel as the web frontend, Go for the backend HTTP server to handle scan requests, and Python for network scanning (leveraging Nmap). The setup is designed to scan a network for open ports and services on IoT devices, helping to identify potential security vulnerabilities.

## Project Structure
```
IOT-VULN-SCANNER/
├── go/
│   ├── go.mod                # Go module file for dependencies
│   └── app/
│       └── server.go         # Go HTTP server for handling scan requests
├── laravel/
│   ├── app/                  # Laravel application files
│   ├── routes/               # Routes definition
│   └── ...                   # Other Laravel directories and config files
├── python/
│   ├── requirements.txt      # Python dependencies
│   └── scanner_service.py    # Python script that performs the scan using Nmap
├── run.sh                    # Script to start Go, Python, and Laravel services
├── LICENSE                   # License file
└── README.md                 # Project documentation
```

## Requirements
- **Go**: Version 1.16 or later
- **Python**: Version 3.7 or later
- **PHP**: Version 7.4 or later (for Laravel)
- **Nmap**: Ensure Nmap is installed on the system for network scanning
- **Composer**: To manage Laravel dependencies

```bash
sudo apt install nmap php composer
``` 

## Setup Instructions

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/izzuddinafif/iot-vuln-scanner.git
   cd iot-vuln-scanner
   ```

2. **Install Python Dependencies**:
   Navigate to the `python` directory and install the required packages:

   ```bash
   pip install -r python/requirements.txt
   ```

3. **Install Laravel Dependencies**:
   In the `laravel` directory, install dependencies and set up the `.env` file:

   ```bash
   cd laravel
   cp .env.example .env
   composer install
   php artisan key:generate
   ```

4. **Run the Project**:
   Use the `run.sh` script to start all services (Go, Python, and Laravel).

   ```bash
   chmod +x run.sh
   ./run.sh [optional IP range]
   ```

   - If no IP range is specified, it defaults to `192.168.1.0/24`.

   - Example:

     ```bash
     ./run.sh 192.168.217.0/24
     ```

## Usage

Once the services are running, you can access the Laravel frontend at `https://iot-vuln-scanner.izzuddinafif.com` to initiate network scans and view results.

### Initiating a Scan

1. **From the Web Interface**:
   - Go to `https://iot-vuln-scanner.izzuddinafif.com` and click on "Start Scan" to initiate a scan.

2. **Using CURL**:
   - Initiate a scan with the default IP range:
     ```bash
     curl "http://localhost:8080/scan"
     ```
   - Specify a target IP range:
     ```bash
     curl "http://localhost:8080/scan?target=192.168.1.0/24"
     ```

The Go server forwards the scan request to the Python service, which performs the scan with Nmap and returns results in JSON format.

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

- **Change Default IP Range**: Modify the default range in `server.go` or pass a range to `run.sh` when starting.
- **Adjust Laravel Frontend**: Customize `laravel/resources` for frontend adjustments.
- **Add Scanning Options**: Modify `scanner_service.py` to add more Nmap options or use Nmap’s scripting engine for specific vulnerability checks.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.

## Contributing

Contributions are welcome! Feel free to submit issues or pull requests to improve the project.
