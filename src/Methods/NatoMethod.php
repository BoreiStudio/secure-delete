<?php

namespace Boreistudio\SecureDelete\Methods;

use Boreistudio\SecureDelete\Methods\interfaces\SecureDeleteMethodInterface;

class NatoMethod implements SecureDeleteMethodInterface
{
    public static function delete(string $target): bool
    {
        if (!file_exists($target)) {
            throw new \RuntimeException("File not found: $target");
        }

        if (!is_writable($target)) {
            throw new \RuntimeException("Write permission denied: $target");
        }

        // 1. Sobrescritura en 4 fases
        $patterns = self::getPatterns();
        $fileSize = filesize($target);
        
        foreach ($patterns as $pattern) {
            if (!self::overwriteFile($target, $pattern, $fileSize)) {
                return false;
            }
        }

        // 2. Verificación NATO (requisito SDIP-27)
        if (!self::verifyOverwrite($target)) {
            throw new \RuntimeException("NATO verification failed: $target");
        }

        // 3. Eliminación final
        return unlink($target);
    }

    private static function overwriteFile(string $path, string $pattern, int $fileSize): bool
    {
        $handle = fopen($path, 'wb');
        if ($handle === false) return false;

        $blockSize = 4096; // 4KB blocks
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

    private static function verifyOverwrite(string $path): bool
    {
        // Muestra aleatoria del 10% del archivo
        $fileSize = filesize($path);
        $sampleSize = max(4096, (int)($fileSize * 0.1)); // Mínimo 4KB
        $offset = random_int(0, $fileSize - $sampleSize);
        
        $sample = file_get_contents(
            $path, 
            false, 
            null, 
            $offset, 
            $sampleSize
        );

        // Verificar que no queden datos originales
        return !preg_match('/[^\x00\xFF\x96]/', $sample);
    }

    public static function getPatterns(): array
    {
        return [
            "\x00", // Paso 1: Todos ceros
            "\xFF", // Paso 2: Todos unos
            "\x96", // Paso 3: Patrón NATO específico
            random_bytes(1)  // Paso 4: Aleatorio
        ];
    }
}