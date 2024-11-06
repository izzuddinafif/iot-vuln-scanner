from flask import Flask, jsonify, request
import nmap
import logging
import socket

app = Flask(__name__)

# Initialize Nmap scanner
nm = nmap.PortScanner()

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Get the server's own IP address to exclude it from the scan
def get_server_ip():
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    try:
        # Connect to an external IP to get the local IP (doesn't send actual data)
        s.connect(("8.8.8.8", 80))
        ip = s.getsockname()[0]
    except Exception as e:
        logging.error(f"Could not determine server IP: {e}")
        ip = None
    finally:
        s.close()
    return ip

server_ip = get_server_ip()
if server_ip:
    logging.info(f"Server IP determined as {server_ip}, excluding from scan.")

# Function to perform an Nmap scan on a subnet or IP
def perform_scan(target):
    try:
        logging.info(f"Starting scan for target: {target}")
        # Exclude the server's IP from the scan
        exclude_arg = f"--exclude {server_ip}" if server_ip else ""
        # Scan the subnet or single IP, excluding the server IP if known
        nm.scan(hosts=target, arguments=f"-sV {exclude_arg}")
        
        scan_result = {}
        for host in nm.all_hosts():
            logging.info(f"Scanning host: {host}")
            # Safely access attributes using .get
            scan_result[host] = {
                'hostnames': nm[host].hostnames() if 'hostnames' in nm[host] else [],
                'addresses': nm[host].get('addresses', {}),
                'ports': [{
                    'port': p, 
                    'state': nm[host][proto][p]['state'],
                    'service': nm[host][proto][p]['name']
                } for proto in nm[host].all_protocols() for p in nm[host][proto].keys()]
            }
        
        logging.info("Scan completed successfully")
        return scan_result
    except Exception as e:
        logging.error(f"Error during scan: {e}")
        return {"error": str(e)}

# Endpoint to handle scan requests
@app.route('/run-scan', methods=['POST'])
def run_scan():
    data = request.get_json()
    target = data.get("target")
    if not target:
        logging.warning("No target specified in request")
        return jsonify({"error": "Target IP or subnet required"}), 400
    
    logging.info(f"Received scan request for target: {target}")
    scan_results = perform_scan(target)
    logging.info(f"Returning scan results for target: {target}")
    return jsonify(scan_results)

# Run the Flask app
if __name__ == '__main__':
    logging.info("Starting Python scanner service on port 5001")
    app.run(host='0.0.0.0', port=5001)
