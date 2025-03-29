<?php

namespace Boreistudio\SecureDelete;

use Boreistudio\SecureDelete\Methods\DoD5220Method;
use Boreistudio\SecureDelete\Methods\GutmannMethod;
use Boreistudio\SecureDelete\Methods\RandomMethod;

class SecureDelete
{
    private const METHOD_MAP = [
        'dod_5220' => DoD5220Method::class,
        'gutmann'  => GutmannMethod::class,
        'bsi'      => BsiMethod::class,
        'vsitr'    => VsitrMethod::class,
        'nist'     => NistMethod::class,
        'rcmp'     => RcmpMethod::class,
        'nato' => NatoMethod::class,
        'random'   => RandomMethod::class,
    ];

    public static function wipeFile(string $path, string $method = 'dod_5220'): bool
    {
        if (!isset(self::METHOD_MAP[$method])) {
            throw new \InvalidArgumentException("Método no soportado: $method");
        }

        return self::METHOD_MAP[$method]::delete($path);
    }

    public static function wipeDirectory(string $dirPath, string $method = 'dod_5220'): bool
    {
        if (!is_dir($dirPath)) {
            throw new \RuntimeException("El directorio no existe: $dirPath");
        }
    
        if (!isset(self::METHOD_MAP[$method])) {
            throw new \InvalidArgumentException("Método no soportado: $method");
        }
    
        try {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
    
            $allSuccess = true;
    
            foreach ($files as $file) {
                if ($file->isFile()) {
                    if (!self::METHOD_MAP[$method]::delete($file->getPathname())) {
                        $allSuccess = false;
                    }
                }
            }
    
            return $allSuccess && @rmdir($dirPath);
    
        } catch (\UnexpectedValueException $e) {
            throw new \RuntimeException("Error al acceder al directorio: " . $e->getMessage());
        }
    }
}