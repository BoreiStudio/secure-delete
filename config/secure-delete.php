<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Logging Configuration
    |--------------------------------------------------------------------------
    | Controls the audit trail functionality for secure deletions.
    */
    'database' => [
        'enabled' => true,          // Enable/disable database logging
        'store_checksum' => true,   // Store SHA-256 hash of original file
        'store_filesize' => true,   // Record original file size in bytes
        'track_user' => true,       // Automatically log executing user
    ],

    /*
    |--------------------------------------------------------------------------
    | NATO Standard Specific Settings
    |--------------------------------------------------------------------------
    | Applies only when using 'nato' deletion method.
    */
    'nato' => [
        'verification_sample_size' => 0.1,  // Percentage of file to verify (0.1 = 10%)
        'strict_mode' => true,              // Abort if verification fails
        'log_verification' => storage_path('logs/nato_secure_delete.log') // Verification log path
    ],

    /*
    |--------------------------------------------------------------------------
    | Checksum Calculation Limit
    |--------------------------------------------------------------------------
    | Maximum file size (in MB) for checksum generation.
    | Files larger than this will skip checksum storage.
    */
    'max_file_size_checksum' => 500,

    /*
    |--------------------------------------------------------------------------
    | Available Standards
    |--------------------------------------------------------------------------
    | Enable/disable specific deletion standards.
    */
    'standards' => [
        'dod_5220' => true,   // US Department of Defense 3-pass
        'gutmann' => false,    // 35-pass secure deletion (resource intensive)
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Deletion Method
    |--------------------------------------------------------------------------
    | The standard used when no method is specified.
    | Must be one of the enabled standards above.
    */
    'default_method' => 'dod_5220',
];