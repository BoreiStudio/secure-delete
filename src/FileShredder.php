<?php

namespace Boreistudio\SecureDelete;

class FileShredder
{
    public static function overwriteFile(string $path, string $pattern): bool
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Archivo no encontrado: $path");
        }

        $fileSize = filesize($path);
        $handle = fopen($path, 'wb');

        if ($handle === false) {
            return false;
        }

        // Escribir en bloques de 4KB para eficiencia
        $blockSize = 4096;
        $blocks = ceil($fileSize / $blockSize);
        
        for ($i = 0; $i < $blocks; $i++) {
            fwrite($handle, str_repeat($pattern, $blockSize));
        }

        fclose($handle);
        return true;
    }

    public static function wipeDirectory(string $dirPath, array $patterns): bool
    {
        if (!is_dir($dirPath)) {
            throw new \RuntimeException("Directorio no encontrado: $dirPath");
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isFile()) {
                foreach ($patterns as $pattern) {
                    self::overwriteFile($file->getPathname(), $pattern);
                }
                unlink($file->getPathname());
            }
        }

        return rmdir($dirPath);
    }
}