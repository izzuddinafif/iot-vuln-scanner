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

    Go: Version 1.16 or later
    Python: Version 3.7 or later
    Nmap: Ensure Nmap is installed on the system for network scanning

`sudo apt install nmap`


## Setup Instructions

    Clone the Repository:

1. git clone https://github.com/yourusername/IOT-VULN-SCANNER.git
cd IOT-VULN-SCANNER

2. Install Python Dependencies: Navigate to the python directory and install the required packages using requirements.txt.

`pip install -r python/requirements.txt`

3. Run the Project: Use the run.sh script to start both the Go and Python services.

`./run.sh [optional IP range]`

- If no IP range is specified, it will default to 192.168.1.0/24.

- Example:

`./run.sh 192.168.217.0/24`