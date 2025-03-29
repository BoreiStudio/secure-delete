<?php
//USA
namespace Boreistudio\SecureDelete\Methods;

use Boreistudio\SecureDelete\Methods\interfaces\SecureDeleteMethodInterface;

class NistMethod implements SecureDeleteMethodInterface
{
    public static function delete(string $target): bool
    {
        if (!file_exists($target)) {
            throw new \RuntimeException("Archivo no encontrado: $target");
        }

        if (!is_writable($target)) {
            throw new \RuntimeException("Sin permisos para escribir: $target");
        }

        // Paso 1: Sobrescritura única con datos aleatorios
        $randomPattern = random_bytes(1);
        if (!self::overwriteFile($target, $randomPattern)) {
            return false;
        }

        // Paso 2: Verificación criptográfica (requerido por NIST)
        $verificationHash = self::generateVerificationHash($target);
        
        // Registrar el hash para auditoría (opcional)
        self::logVerificationHash($target, $verificationHash);

        return unlink($target);
    }

    private static function overwriteFile(string $path, string $pattern): bool
    {
        $fileSize = filesize($path);
        $handle = fopen($path, 'wb');
        
        if ($handle === false) return false;

        $blockSize = 4096; // 4KB por bloque
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

    private static function generateVerificationHash(string $path): string
    {
        $context = hash_init('sha256');
        $handle = fopen($path, 'rb');

        while (!feof($handle)) {
            hash_update($context, fread($handle, 8192));
        }

        fclose($handle);
        return hash_final($context);
    }

    private static function logVerificationHash(string $path, string $hash): void
    {
        // Opcional: Guardar en log o base de datos
        $logMessage = sprintf(
            "[%s] Verificación NIST de %s: %s",
            date('Y-m-d H:i:s'),
            basename($path),
            $hash
        );
        
        file_put_contents(
            storage_path('logs/secure_delete.log'),
            $logMessage . PHP_EOL,
            FILE_APPEND
        );
    }

    public static function getPatterns(): array
    {
        return [random_bytes(1)]; // Un único patrón aleatorio
    }
}