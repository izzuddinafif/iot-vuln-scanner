package main

import (
	"bytes"
	"encoding/json"
	"flag"
	"io"
	"log"
	"net/http"
)

type ScanRequest struct {
	Target string `json:"target"`
}

func enableCors(w *http.ResponseWriter) {
	(*w).Header().Set("Access-Control-Allow-Origin", "*")
	(*w).Header().Set("Access-Control-Allow-Methods", "POST, GET, OPTIONS, PUT, DELETE")
	(*w).Header().Set("Access-Control-Allow-Headers", "Content-Type")
}

func scanHandler(w http.ResponseWriter, r *http.Request, defaultTarget string) {
	enableCors(&w)

	if r.Method == "OPTIONS" {
		w.WriteHeader(http.StatusOK)
		return
	}

	log.Println("Received request for scan")

	target := defaultTarget

	scanReq := ScanRequest{Target: target}
	reqBody, err := json.Marshal(scanReq)
	if err != nil {
		log.Printf("Failed to encode request: %v", err)
		http.Error(w, "Failed to encode request", http.StatusInternalServerError)
		return
	}

	log.Printf("Sending scan request to Python service for target: %s", target)
	resp, err := http.Post("http://localhost:5001/run-scan", "application/json", bytes.NewBuffer(reqBody))
	if err != nil {
		log.Printf("Failed to connect to Python scanner service: %v", err)
		http.Error(w, "Failed to connect to scanner service", http.StatusInternalServerError)
		return
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		log.Printf("Failed to read response from Python service: %v", err)
		http.Error(w, "Failed to read response", http.StatusInternalServerError)
		return
	}

	log.Println("Successfully received response from Python service")

	w.Header().Set("Content-Type", "application/json")
	_, err = w.Write(body)
	if err != nil {
		log.Printf("Failed to write response to client: %v", err)
	} else {
		log.Println("Response successfully sent to client")
	}
}

func main() {
	defaultTarget := flag.String("target", "192.168.217.0/24", "Default target IP range for scanning")
	flag.Parse()

	http.HandleFunc("/scan", func(w http.ResponseWriter, r *http.Request) {
		scanHandler(w, r, *defaultTarget)
	})

	log.Printf("Go server starting, listening on port 8080 with default target: %s", *defaultTarget)
	log.Fatal(http.ListenAndServe(":8080", nil))
}
