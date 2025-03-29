<?php

return [
    'database' => [
        'enabled' => true,
        'store_checksum' => true,  // Guardar hash SHA-256
        'store_filesize' => true,  // Guardar tamaño de archivo
        'track_user' => true,      // Registrar usuario automáticamente
    ],
    'nato' => [
        'verification_sample_size' => 0.1, // 10% del archivo
        'strict_mode' => true, // Fallar si la verificación no pasa
        'log_verification' => storage_path('logs/nato_secure_delete.log')
    ],
    'max_file_size_checksum' => 500, // Tamaño máximo en MB para calcular checksum
    'standards' => [
        'dod_5220' => true,         // Usar DoD 5220.22-M
        'gutmann' => false,         // ¿Incluir estándar Gutmann (35 pasos)?
    ],
    'default_method' => 'dod_5220', // Método por defecto
];