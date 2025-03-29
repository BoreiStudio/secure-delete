<?php
//Australia
namespace Boreistudio\SecureDelete\Methods;

use Boreistudio\SecureDelete\Methods\interfaces\SecureDeleteMethodInterface;

class VsitrMethod implements SecureDeleteMethodInterface
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
        $fileSize = filesize($target);
        $success = true;

        foreach ($patterns as $pattern) {
            if (!self::overwriteWithPattern($target, $pattern, $fileSize)) {
                $success = false;
                break;
            }
        }

        return $success && unlink($target);
    }

    private static function overwriteWithPattern(string $path, string $pattern, int $fileSize): bool
    {
        $handle = fopen($path, 'wb');
        if ($handle === false) return false;

        $blockSize = 4096; // Bloques de 4KB para eficiencia
        $blocks = ceil($fileSize / $blockSize);
        $patternBlock = str_repeat($pattern, $blockSize);

        for ($i = 0; $i < $blocks; $i++) {
            if (fwrite($handle, $patternBlock) === false) {
                fclose($handle);
                return false;
            }
        }

        fclose($handle);
        return true;
    }

    public static function getPatterns(): array
    {
        return [
            "\x00", // Paso 1: Todos ceros (0x00)
            "\xFF", // Paso 2: Todos unos (0xFF)
            "\xAA"  // Paso 3: Patrón alternado (10101010)
        ];
    }
}