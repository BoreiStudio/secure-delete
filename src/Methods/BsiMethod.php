<?php
//Alemania
namespace Boreistudio\SecureDelete\Methods;

use Boreistudio\SecureDelete\Methods\interfaces\SecureDeleteMethodInterface;

class BsiMethod implements SecureDeleteMethodInterface
{
    public static function delete(string $target): bool
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
        
        if ($handle === false) {
            return false;
        }

        $blockSize = 4096; // 4KB por bloque
        $blocks = ceil($fileSize / $blockSize);
        
        for ($i = 0; $i < $blocks; $i++) {
            fwrite($handle, str_repeat($pattern, $blockSize));
        }

        fclose($handle);
        return true;
    }

    public static function getPatterns(): array
    {
        return [
            "\x00", // Paso 1: Todos ceros
            "\xFF", // Paso 2: Todos unos
            "\xAA", // Paso 3: 10101010
            "\x55", // Paso 4: 01010101
            "\x92", // Paso 5: 10010010
            "\x49", // Paso 6: 01001001
            "\x24"  // Paso 7: 00100100
        ];
    }
}