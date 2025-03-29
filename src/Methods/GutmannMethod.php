<?php
//Nueva zelanda
namespace Boreistudio\SecureDelete\Methods;

use Boreistudio\SecureDelete\Methods\interfaces\SecureDeleteMethodInterface;

class GutmannMethod implements SecureDeleteMethodInterface
{
    public static function delete(string $target): bool  // Nombre de parámetro actualizado
    {
        if (!file_exists($target)) {
            throw new \RuntimeException("Archivo no encontrado: $target");
        }

        if (!is_writable($target)) {
            throw new \RuntimeException("Sin permisos para escribir: $target");
        }

        $patterns = self::getPatterns();
        $success = true;

        foreach ($patterns as $pattern) {
            if (!self::overwrite($target, $pattern)) {
                $success = false;
                break;
            }
        }

        return $success && unlink($target);
    }

    private static function overwrite(string $path, string $pattern): bool
    {
        $fileSize = filesize($path);
        $handle = fopen($path, 'wb');
        
        if ($handle === false) return false;
        
        $written = fwrite($handle, str_repeat($pattern, $fileSize));
        fclose($handle);
        
        return $written !== false;
    }

    public static function getPatterns(): array
    {
        return [
            "\x00", "\xFF",                                               // 1-2: Ceros y unos
            "\xAA", "\x55", "\x92", "\x49", "\x24",                       // 3-7: Patrones comunes
            "\x6D", "\xB6", "\xDB", "\x6E", "\xB7",                       // 8-12
            "\x11", "\x22", "\x33", "\x44", "\x55", "\x66", "\x77",       // 13-19
            "\x88", "\x99", "\xBB", "\xCC", "\xDD", "\xEE", "\xFE",       // 20-26
            "\xEF", "\xFD", "\xFB", "\xF7", "\xDF", "\xBF", "\x7F",       // 27-33
            "\x00", "\xFF"                                                // 34-35: Final con ceros y unos
        ];
    }
}