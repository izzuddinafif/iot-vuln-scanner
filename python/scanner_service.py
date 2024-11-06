from flask import Flask, jsonify, request
import nmap
import logging

app = Flask(__name__)

# Initialize Nmap scanner
nm = nmap.PortScanner()

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Function to perform an Nmap scan on a subnet or IP
def perform_scan(target):
    try:
        logging.info(f"Starting scan for target: {target}")
        # Scan the entire subnet or single IP
        nm.scan(hosts=target, arguments='-sV')
        
        scan_result = {}
        for host in nm.all_hosts():
            logging.info(f"Scanning host: {host}")
            # Use .get to safely handle missing attributes
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
