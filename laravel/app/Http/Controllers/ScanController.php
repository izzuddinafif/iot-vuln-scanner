<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    public function startScan()
    {
        // Menjalankan perintah Nmap (sesuaikan targetnya)
        $output = [];
        exec('nmap -sV <target-ip>', $output);

        // Simpan hasil scan ke database
        DB::table('scan_results')->insert([
            'results' => json_encode($output),
            'created_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function getScanResults()
    {
        // Mengambil hasil scan terbaru
        $result = DB::table('scan_results')->orderBy('created_at', 'desc')->first();
        return response()->json($result);
    }
}
