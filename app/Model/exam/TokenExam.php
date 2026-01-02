<?php
namespace App\Model\exam;

class TokenExam {
    function generateToken($nomorUrut, $kategori) {
        $data = json_encode([
            'nomorUrut' => $nomorUrut,
            'kategori' => $kategori,
            'timestamp' => time()
        ]);
        $key = 'secret_key';
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, substr(hash('sha256', $key), 0, 16));
        return base64_encode($encrypted);
    }
    function validateToken($token) {
        $key = 'secret_key';
        $decrypted = openssl_decrypt(base64_decode($token), 'AES-256-CBC', $key, 0, substr(hash('sha256', $key), 0, 16));
        if (!$decrypted) {
            throw new \Exception('Token tidak valid');
        }
        $data = json_decode($decrypted, true);
        if (!$data || !isset($data['nomorUrut'], $data['kategori'], $data['timestamp'])) {
            throw new \Exception('Token rusak atau data tidak lengkap');
        }
    
        if (time() - $data['timestamp'] > 3600) {
            throw new \Exception('Token telah kedaluwarsa');
        }
    
        return $data;
    }
    
    
}